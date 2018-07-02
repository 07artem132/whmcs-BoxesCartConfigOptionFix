<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 21.06.2018
 * Time: 21:41
 */

use WHMCS\Database\Capsule;
use WHMCS\Product\Product;

function BoxesCartConfigOptionFix_config() {
	return [
		"name"        => "Boxes cart config option fix total",
		"description" => "",
		"version"     => "1",
		"author"      => "service-voice",
		"fields"      => [

		]
	];
}

function BoxesCartConfigOptionFix_clientarea( $vars ) {
	$price = [
		'monthly'      => 0.0,
		'quarterly'    => 0.0,
		'semiannually' => 0.0,
		'annually'     => 0.0,
		'biennially '  => 0.0,
		'triennially'  => 0.0,
	];

	$setupFee = [
		'monthly'      => 0.0,
		'quarterly'    => 0.0,
		'semiannually' => 0.0,
		'annually'     => 0.0,
		'biennially '  => 0.0,
		'triennially'  => 0.0,
	];

	$pid = $_SESSION['cart']['products'][ count( $_SESSION['cart']['products'] ) - 1 ]['pid'];

	$productPricing = Capsule::table( 'tblpricing' )->where( 'relid', $pid )->where( 'type', 'product' )->first();

	$setupFee['monthly'] += $productPricing->msetupfee;
	$price['monthly']    += $productPricing->monthly;

	$setupFee['quarterly'] += $productPricing->quarterly;
	$price['quarterly']    += $productPricing->quarterly;

	$setupFee['semiannually'] += $productPricing->semiannually;
	$price['semiannually']    += $productPricing->semiannually;

	$setupFee['annually'] += $productPricing->annually;
	$price['annually']    += $productPricing->annually;

	$setupFee['biennially'] += $productPricing->biennially;
	$price['biennially']    += $productPricing->biennially;

	$setupFee['triennially'] += $productPricing->triennially;
	$price['triennially']    += $productPricing->triennially;

	$configOptionList = Capsule::table( 'tblproductconfiglinks' )->where( 'pid', $pid )->get();

	foreach ( $configOptionList as $item ) {
		$GroupConfigOptions = Capsule::table( 'tblproductconfigoptions' )->where( 'gid', $item->gid )->get();
		foreach ( $GroupConfigOptions as $GroupConfigOptionsSub ) {
			$options = Capsule::table( 'tblproductconfigoptionssub' )->where( 'configid', $GroupConfigOptionsSub->id )->get();
			foreach ( $options as $option ) {
				if ( $GroupConfigOptionsSub->optiontype == 1 & $_GET['configoption'][ $GroupConfigOptionsSub->id ] != $option->id ) {
					continue;
				}
				$optionCost = Capsule::table( 'tblpricing' )->where( 'relid', $option->id )->where( 'type', 'configoptions' )->first();

				if ( $GroupConfigOptionsSub->optiontype == 4 ) {
					if ( $_GET['configoption'][ $GroupConfigOptionsSub->id ] > (int) $GroupConfigOptionsSub->qtyminimum ) {
						$price['monthly']      += (float) $optionCost->monthly * (int) $_GET['configoption'][ $GroupConfigOptionsSub->id ];
						$price['quarterly']    += (float) $optionCost->quarterly * (int) $_GET['configoption'][ $GroupConfigOptionsSub->id ];
						$price['semiannually'] += (float) $optionCost->semiannually * (int) $_GET['configoption'][ $GroupConfigOptionsSub->id ];
						$price['annually']     += (float) $optionCost->annually * (int) $_GET['configoption'][ $GroupConfigOptionsSub->id ];
						$price['biennially']   += (float) $optionCost->biennially * (int) $_GET['configoption'][ $GroupConfigOptionsSub->id ];
						$price['triennially']  += (float) $optionCost->triennially * (int) $_GET['configoption'][ $GroupConfigOptionsSub->id ];
					} else {
						$price['monthly']      += (float) $optionCost->monthly * (int) $GroupConfigOptionsSub->qtyminimum;
						$price['quarterly']    += (float) $optionCost->quarterly * (int) $GroupConfigOptionsSub->qtyminimum;
						$price['semiannually'] += (float) $optionCost->semiannually * (int) $GroupConfigOptionsSub->qtyminimum;
						$price['annually']     += (float) $optionCost->annually * (int) $GroupConfigOptionsSub->qtyminimum;
						$price['biennially']   += (float) $optionCost->biennially * (int) $GroupConfigOptionsSub->qtyminimum;
						$price['triennially']  += (float) $optionCost->triennially * (int) $GroupConfigOptionsSub->qtyminimum;
					}
				} else {
					$price['monthly']      += (float) $optionCost->monthly;
					$price['quarterly']    += (float) $optionCost->quarterly;
					$price['semiannually'] += (float) $optionCost->semiannually;
					$price['annually']     += (float) $optionCost->annually;
					$price['biennially']   += (float) $optionCost->biennially;
					$price['triennially']  += (float) $optionCost->triennially;
				}
				$setupFee['monthly']      += (float) $optionCost->msetupfee;
				$setupFee['quarterly']    += (float) $optionCost->quarterly;
				$setupFee['semiannually'] += (float) $optionCost->semiannually;
				$setupFee['annually']     += (float) $optionCost->annually;
				$setupFee['biennially']   += (float) $optionCost->biennially;
				$setupFee['triennially']  += (float) $optionCost->triennially;
			}
		}

	}

	echo json_encode( [
		'price'    => $price,
		'setupFee' => $setupFee
	] );
	die();
}