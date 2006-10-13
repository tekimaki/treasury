<?php
/**
 * @version      $Header: /cvsroot/bitweaver/_bit_treasury/view.php,v 1.7 2006/10/13 12:47:20 lsces Exp $
 *
 * @author       xing  <xing@synapse.plus.com>
 * @package      treasury
 * @copyright    2003-2006 bitweaver
 * @license      LGPL {@link http://www.gnu.org/licenses/lgpl.html}
 **/

/**
 * Setup
 */ 
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'treasury' );

require_once( TREASURY_PKG_PATH.'TreasuryGallery.php');
require_once( TREASURY_PKG_PATH.'TreasuryItem.php');
require_once( TREASURY_PKG_PATH.'gallery_lookup_inc.php' );

$feedback = array();

// replace any user permissions with custom ones if we have set them
$gContent->updateUserPermissions();
$gBitSystem->verifyPermission( 'p_treasury_view_gallery' );

if( !empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'delete' ) {
	$feedback['success'] = '';
	foreach( $_REQUEST['content_ids'] as $contentId ) {
		if( @BitBase::verifyId( $contentId ) ) {
			if( $galleryItem = $gLibertySystem->getLibertyObject( $contentId ) ) {
				$galleryItem->load();
				$title = $galleryItem->getTitle();
				if( $galleryItem->expunge() ) {
					$feedback['success'] .= "<li>$title</li>";
				}
			}
		}
	}

	if( !empty( $feedback['success'] ) ) {
		$feedback['success'] = tra( 'The following items were successfully deleted' ).':<ul>'.$feedback['success'].'</ul>';
	}
}

// used to display the newly updated version of an image
if( !empty( $_REQUEST['refresh'] ) ) {
	$gBitSmarty->assign( 'refresh', '?refresh='.time() );
}

// services
$displayHash = array( 'perm_name' => 'p_treasury_gallery_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );

// set up structure related stuff
global $gStructure;
if( empty( $gContent->mInfo['root_structure_id'] ) || !@BitBase::verifyId( $gContent->mInfo['root_structure_id'] ) ) {
	header( "Location:".TREASURY_PKG_URL."index.php" );
}

$gStructure = new LibertyStructure( $gContent->mInfo['root_structure_id'] );
$gStructure->load();

// confirm that structure is valid
if( empty( $gStructure ) || !$gStructure->isValid() ) {
	$gBitSystem->fatalError( 'Invalid structure' );
}

$gBitSmarty->assign_by_ref( 'gStructure', $gStructure );
$gBitSmarty->assign( 'structureInfo', $gStructure->mInfo );
$gBitSmarty->assign( 'subtree', $gStructure->getSubTree( $gStructure->mStructureId ) );

$listHash = $_REQUEST;
$listHash['root_structure_id'] = $gContent->mInfo['root_structure_id'];
$listHash['structure_id']      = $gContent->mInfo['structure_id'];

$gContent->loadItems( $listHash );

// pagination related settings
$listHash['listInfo']['parameters']['structure_id'] = $gContent->mStructureId;
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
$gBitSmarty->assign( 'feedback', $feedback );

$gContent->addHit();

// Display the template
$gBitSystem->display( 'bitpackage:treasury/view_gallery.tpl', tra( 'View Gallery' ) );
?>
