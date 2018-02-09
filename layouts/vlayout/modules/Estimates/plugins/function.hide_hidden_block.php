<?php

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.hide_hidden_block.php
 * Type:     function
 * Name:     hide_hidden_block
 * Purpose:  Returns "hide" if the block is hidden.
 * -------------------------------------------------------------
 */
function smarty_function_hide_hidden_block($params, $smarty)
{
    $block_label = $params['block_label'] ?: null;
    $hidden_blocks = $params['hidden_blocks'] ?: array();
    $tariff_blocks = $params['tariff_blocks'] ?: array();

    $current_tariff_blocks = $tariff_blocks['currentTariffBlocks'] ?: null;
    $inactive_tariff_blocks = $tariff_blocks['inactiveTariffBlocks'] ?: null;

    if (!is_array($hidden_blocks)) {
        return;
    }

    if (in_array($block_label, $hidden_blocks)
        || (is_array($inactive_tariff_blocks) && in_array($block_label, $inactive_tariff_blocks))
        || (is_array($current_tariff_blocks) && array_key_exists($block_label, $current_tariff_blocks) && $current_tariff_blocks[$block_label] == 0)
    ) {
        return 'hide';
    }

    return;
}
