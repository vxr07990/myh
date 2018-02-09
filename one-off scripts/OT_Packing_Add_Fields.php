<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Quotes');

$block1 = new Vtiger_Block();
$block1->label = 'OT Packing';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'Dish Pack';
$field1->name = 'ot_dish_pack_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dish_pack_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Dish Pack (Unpack Qty)';
$field1->name = 'ot_dish_pack_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dish_pack_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '1.5';
$field1->name = 'ot_one_half_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_one_half_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '1.5 (Unpack Qty)';
$field1->name = 'ot_one_half_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_one_half_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '3.0';
$field1->name = 'ot_three_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_three_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '3.0 (Unpack Qty)';
$field1->name = 'ot_three_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_three_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '4.5';
$field1->name = 'ot_four_half_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_four_half_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '4.5 (Unpack Qty)';
$field1->name = 'ot_four_half_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_four_half_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.0';
$field1->name = 'ot_six_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_six_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.0 (Unpack Qty)';
$field1->name = 'ot_six_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_six_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.5';
$field1->name = 'ot_six_half_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_six_half_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.5 (Unpack Qty)';
$field1->name = 'ot_six_half_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_six_half_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Wardrobe';
$field1->name = 'ot_wardrobe_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_wardrobe_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Wardrobe (Unpack Qty)';
$field1->name = 'ot_wardrobe_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_wardrobe_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Crib';
$field1->name = 'ot_crib_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_crib_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Crib (Unpack Qty)';
$field1->name = 'ot_crib_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_crib_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Single/Twin';
$field1->name = 'ot_single_twin_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_single_twin_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Single/Twin (Unpack Qty)';
$field1->name = 'ot_single_twin_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_single_twin_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Long';
$field1->name = 'ot_long_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_long_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Long (Unpack Qty)';
$field1->name = 'ot_long_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_long_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double Bed';
$field1->name = 'ot_double_bed_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_double_bed_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double Bed (Unpack Qty)';
$field1->name = 'ot_double_bed_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_double_bed_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'King/Queen';
$field1->name = 'ot_king_queen_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_king_queen_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'King/Queen (Unpack Qty)';
$field1->name = 'ot_king_queen_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_king_queen_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mirror';
$field1->name = 'ot_mirror_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_mirror_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);
$field1 = new Vtiger_Field();
$field1->label = 'Mirror (Unpack Qty)';
$field1->name = 'ot_mirror_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_mirror_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Grandfather Clock';
$field1->name = 'ot_gf_clock_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_gf_clock_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Grandfather Clock (Unpack Qty)';
$field1->name = 'ot_gf_clock_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_gf_clock_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'TV Carton';
$field1->name = 'ot_tv_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_tv_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'TV Carton (Unpack Qty)';
$field1->name = 'ot_tv_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_tv_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ironing Board';
$field1->name = 'ot_ironing_board_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_ironing_board_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ironing Board (Unpack Qty)';
$field1->name = 'ot_ironing_board_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_ironing_board_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Lamp';
$field1->name = 'ot_lamp_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_lamp_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Lamp (Unpack Qty)';
$field1->name = 'ot_lamp_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_lamp_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Pole Lamp';
$field1->name = 'ot_pole_lamp_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_pole_lamp_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Pole Lamp (Unpack Qty)';
$field1->name = 'ot_pole_lamp_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_pole_lamp_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ski';
$field1->name = 'ot_ski_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_ski_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ski (Unpack Qty)';
$field1->name = 'ot_ski_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_ski_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Tea Chest';
$field1->name = 'ot_tea_chest_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_tea_chest_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Tea Chest (Unpack Qty)';
$field1->name = 'ot_tea_chest_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_tea_chest_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double < 4 cu. ft.';
$field1->name = 'ot_dbl_less_four_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dbl_less_four_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double < 4 cu. ft. (Unpack Qty)';
$field1->name = 'ot_dbl_less_four_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dbl_less_four_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 4 < 7';
$field1->name = 'ot_dbl_less_seven_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dbl_less_seven_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 4 < 7 (Unpack Qty)';
$field1->name = 'ot_dbl_less_seven_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dbl_less_seven_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 7 < 15';
$field1->name = 'ot_double_less_fifteen_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dbl_less_fifteen_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 7 < 15 (Unpack Qty)';
$field1->name = 'ot_double_less_fifteen_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_dbl_less_fifteen_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mattress Cover';
$field1->name = 'ot_mattress_cover_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_mattress_cover_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mattress Cover (Unpack Qty)';
$field1->name = 'ot_mattress_cover_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_mattress_cover_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Other';
$field1->name = 'ot_other_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_other_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Other (Unpack Qty)';
$field1->name = 'ot_other_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_other_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Heavy Duty';
$field1->name = 'ot_heavy_duty_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_heavy_duty_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Heavy Duty (Unpack Qty)';
$field1->name = 'ot_heavy_duty_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ot_heavy_duty_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);



$block1->save($module);
