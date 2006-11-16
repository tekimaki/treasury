<?php
/**
 * @version      $Header: /cvsroot/bitweaver/_bit_treasury/upload.php,v 1.10 2006/11/16 12:36:17 squareing Exp $
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
require_once( TREASURY_PKG_PATH.'gallery_lookup_inc.php');

// when we're uploading a file, permissions are taken care of in the store() function
if( empty( $_REQUEST['treasury_store'] ) ) {
	// replace any user permissions with custom ones if we have set them
	$gContent->updateUserPermissions();
	$gBitSystem->verifyPermission( 'p_treasury_upload_item' );
}

require_once( LIBERTY_PKG_PATH.'calculate_max_upload_inc.php' );

// turn the max_file_size value into megabytes
$gBitSmarty->assign_by_ref( 'feedback', $feedback = array() );

$listHash['load_only_root'] = TRUE;
$listHash['max_records']    = -1;
$galleryList = $gContent->getList( $listHash );

if( @is_array( $galleryList ) ) {
	foreach( $galleryList as $key => $gallery ) {
		if( empty( $gStructure ) ) {
			$gStructure = new LibertyStructure();
		}
		$galleryList[$key]['subtree'] = $gStructure->getSubTree( $gallery['root_structure_id'] );
	}
}
$gBitSmarty->assign( 'galleryList', $galleryList );

if( !empty( $_REQUEST['content_id'] ) ) {
	$galleryContentIds[] = $_REQUEST['content_id'];
	$gBitSmarty->assign( 'galleryContentIds', $galleryContentIds );
}

if( !empty( $_REQUEST['treasury_store'] ) && !empty( $_FILES ) ) {
	$i = 0;
	foreach( $_FILES as $upload ) {
		if( !empty( $upload['tmp_name'] ) ) {
			// store each file individually
			$treasuryItem = new TreasuryItem();

			// transfer the form data to a store hash
			$storeHash = !empty( $_REQUEST['filedata'][$i] ) ? $_REQUEST['filedata'][$i] : array();

			// add the file details to the store hash
			$storeHash['upload'] = $upload;
			if( $treasuryItem->store( $storeHash ) ) {
				$success = TRUE;
			} else {
				$feedback['error'] = $treasuryItem->mErrors;
			}
			$i++;
		}
	}

	if( empty( $feedback['error'] ) && !empty( $success ) ) {
		header( 'Location: '.TreasuryGallery::getDisplayUrl( $storeHash['galleryContentIds'][0] ) );
die;
	}
}

if( $gBitSystem->isPackageActive( 'gigaupload' ) ) {
	gigaupload_smarty_setup( TREASURY_PKG_URL.'upload.php' );
} elseif( $gBitSystem->isFeatureActive( 'treasury_extended_upload_slots' ) ) {
	$uploadSlots = array();
	$uploadSlots = array_pad( $uploadSlots, 9, 0 );
	$gBitSmarty->assign( 'uploadSlots', $uploadSlots );
} else {
	$gBitSmarty->assign( 'loadMultiFile', TRUE );
}

$gContent->invokeServices( 'content_edit_function' );

$gBitSmarty->assign( 'feedback', !empty( $feedback ) ? $feedback : NULL );

$gBitSystem->display( 'bitpackage:treasury/upload.tpl', tra( 'Upload File' ) );
?>
