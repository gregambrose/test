<?php
/**
* @ iDezender 8.0
* @ Developed by Qarizma
*
* @    Visit our website:
* @    www.iRadikal.com
* @    For cheap decoding service :)
* @    And for the ionCube Decoder!
*/          

	require_once( '../include/startup.php' );
	$user = null;
	$original = new PolicyTransaction( 7868 );
	$original->set( 'ptInvoiceNo', 3007227 );
	$original->set( 'ptSysTran', 7009627 );
	$original->_fldForUpdatedBy = null;
	$original->_fldForUpdatedWhen = null;
	$original->update(  );
	$lastBy = $original->get( 'ptLastUpdateBy' );
	$lastOn = $original->get( 'ptLastUpdateOn' );
	$new1 = new PolicyTransaction( null );
	$new1->fieldNames = $original->fieldNames;
	$new1->setAll( $original->getAll(  ) );
	$new1->set( 'ptCode', 7881 );
	$new1->set( 'ptInvoiceNo', 3007228 );
	$new1->set( 'ptSysTran', 7009628 );
	$new1->set( 'ptClientTran', 10286 );
	$new1->set( 'ptMainInsCoTran', 9151 );
	$new1->set( 'ptLastUpdateOn', $lastOn );
	$new1->set( 'ptLastUpdateBy', $lastBy );
	$new1->_fldForUpdatedBy = null;
	$new1->_fldForUpdatedWhen = null;
	$new1->insert( null );
	$ptCode1 = $new1->getKeyValue(  );
	$new2 = new PolicyTransaction( null );
	$new2->fieldNames = $original->fieldNames;
	$new2->setAll( $original->getAll(  ) );
	$new2->set( 'ptCode', 7882 );
	$new2->set( 'ptInvoiceNo', 3007229 );
	$new2->set( 'ptSysTran', 7009629 );
	$new2->set( 'ptClientTran', 10287 );
	$new2->set( 'ptMainInsCoTran', 9152 );
	$new2->set( 'ptLastUpdateOn', $lastOn );
	$new2->set( 'ptLastUpdateBy', $lastBy );
	$new2->_fldForUpdatedBy = null;
	$new2->_fldForUpdatedWhen = null;
	$new2->insert( null );
	$ptCode2 = $new2->getKeyValue(  );
	$audit1 = new AccountingAudit( 9027680 );
	$audit1->set( 'aaTran', $ptCode1 );
	$audit1->_fldForUpdatedBy = null;
	$audit1->_fldForUpdatedWhen = null;
	$audit1->update(  );
	$audit2 = new AccountingAudit( 9027683 );
	$audit2->set( 'aaTran', $ptCode2 );
	$audit2->_fldForUpdatedBy = null;
	$audit2->_fldForUpdatedWhen = null;
	$audit2->update(  );
	$it1 = new InsCoTransaction( 9151 );
	$it1->set( 'itPolTran', $ptCode1 );
	$it1->_fldForUpdatedBy = null;
	$it1->_fldForUpdatedWhen = null;
	$it1->update(  );
	$it2 = new InsCoTransaction( 9152 );
	$it2->set( 'itPolTran', $ptCode2 );
	$it2->_fldForUpdatedBy = null;
	$it2->_fldForUpdatedWhen = null;
	$it2->update(  );
	$ct1 = new ClientTransaction( 10286 );
	$ct1->set( 'ctPolicyTran', $ptCode1 );
	$ct1->_fldForUpdatedBy = null;
	$ct1->_fldForUpdatedWhen = null;
	$ct1->update(  );
	$ct2 = new ClientTransaction( 10287 );
	$ct2->set( 'ctPolicyTran', $ptCode2 );
	$ct2->_fldForUpdatedBy = null;
	$ct2->_fldForUpdatedWhen = null;
	$ct2->update(  );
?>