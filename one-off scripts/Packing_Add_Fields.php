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
$block1->label = 'Packing';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'Dish Pack (Pack Qty)';
$field1->name = 'dish_pack_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'dish_pack_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Dish Pack (Unpack Qty)';
$field1->name = 'dish_pack_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'dish_pack_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '1.5 (Pack Qty)';
$field1->name = 'one_and_half_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'one_and_half_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '1.5 (Unpack Qty)';
$field1->name = 'one_and_half_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'one_and_half_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '3.0 (Pack Qty)';
$field1->name = 'three_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'three_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '3.0 (Unpack Qty)';
$field1->name = 'three_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'three_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '4.5 (Pack Qty)';
$field1->name = 'four_and_half_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'four_and_half_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '4.5 (Unpack Qty)';
$field1->name = 'four_and_half_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'four_and_half_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.0 (Pack Qty)';
$field1->name = 'six_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'six_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.0 (Unpack Qty)';
$field1->name = 'six_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'six_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.5 (Pack Qty)';
$field1->name = 'six_and_half_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'six_and_half_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = '6.5 (Unpack Qty)';
$field1->name = 'six_and_half_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'six_and_half_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Wardrobe (Pack Qty)';
$field1->name = 'wardrobe_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'wardrobe_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Wardrobe (Unpack Qty)';
$field1->name = 'wardrobe_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'wardrobe_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Crib (Pack Qty)';
$field1->name = 'crib_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'crib_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Crib (Unpack Qty)';
$field1->name = 'crib_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'crib_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Single/Twin (Pack Qty)';
$field1->name = 'single_twin_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'single_twin_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Single/Twin (Unpack Qty)';
$field1->name = 'single_twin_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'single_twin_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Long (Pack Qty)';
$field1->name = 'long_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'long_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Long (Unpack Qty)';
$field1->name = 'long_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'long_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double Bed (Pack Qty)';
$field1->name = 'double_bed_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_bed_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double Bed (Unpack Qty)';
$field1->name = 'double_bed_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_bed_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'King/Queen (Pack Qty)';
$field1->name = 'king_queen_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'king_queen_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'King/Queen (Unpack Qty)';
$field1->name = 'king_queen_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'king_queen_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mirror (Pack Qty)';
$field1->name = 'mirror_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'mirror_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);
$field1 = new Vtiger_Field();
$field1->label = 'Mirror (Unpack Qty)';
$field1->name = 'mirror_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'mirror_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Grandfather Clock (Pack Qty)';
$field1->name = 'gf_clock_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'gf_clock_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Grandfather Clock (Unpack Qty)';
$field1->name = 'gf_clock_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'gf_clock_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'TV Carton (Pack Qty)';
$field1->name = 'tv_carton_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'tv_carton_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'TV Carton (Unpack Qty)';
$field1->name = 'tv_carton_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'tv_carton_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ironing Board (Pack Qty)';
$field1->name = 'ironing_board_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ironing_board_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ironing Board (Unpack Qty)';
$field1->name = 'ironing_board_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ironing_board_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Lamp (Pack Qty)';
$field1->name = 'lamp_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'lamp_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Lamp (Unpack Qty)';
$field1->name = 'lamp_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'lamp_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Pole Lamp (Pack Qty)';
$field1->name = 'pole_lamp_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'pole_lamp_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Pole Lamp (Unpack Qty)';
$field1->name = 'pole_lamp_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'pole_lamp_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ski (Pack Qty)';
$field1->name = 'ski_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ski_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Ski (Unpack Qty)';
$field1->name = 'ski_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'ski_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Tea Chest (Pack Qty)';
$field1->name = 'tea_chest_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'tea_chest_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Tea Chest (Unpack Qty)';
$field1->name = 'tea_chest_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'tea_chest_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double < 4 cu. ft. (Pack Qty)';
$field1->name = 'double_less_four_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_less_four_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double < 4 cu. ft. (Unpack Qty)';
$field1->name = 'double_less_four_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_less_four_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 4 < 7 (Pack Qty)';
$field1->name = 'double_less_seven_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_less_seven_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 4 < 7 (Unpack Qty)';
$field1->name = 'double_less_seven_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_less_seven_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 7 < 15 (Pack Qty)';
$field1->name = 'double_less_fifteen_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_less_fifteen_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Double > 7 < 15 (Unpack Qty)';
$field1->name = 'double_less_fifteen_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'double_less_fifteen_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mattress Cover (Pack Qty)';
$field1->name = 'mattress_cover_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'mattress_cover_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mattress Cover (Unpack Qty)';
$field1->name = 'mattress_cover_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'mattress_cover_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Other (Pack Qty)';
$field1->name = 'other_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'other_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Other (Unpack Qty)';
$field1->name = 'other_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'other_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Heavy Duty (Pack Qty)';
$field1->name = 'heavy_duty_pk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'heavy_duty_pk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Heavy Duty (Unpack Qty)';
$field1->name = 'heavy_duty_unpk_qty';
$field1->table = 'vtiger_quotes';
$field1->column = 'heavy_duty_unpk_qty';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);



$block1->save($module);
