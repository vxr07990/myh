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

$moduleInstance = Vtiger_Module::getInstance('ClaimItems');
if ($moduleInstance) {
    echo "<h2>Updating Module Fields</h2><br>";


    $block = Vtiger_Block::getInstance('LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION', $moduleInstance);
    if ($block) {
        echo "<h3>The LBL_CLAIMITEMS_INFORMATION block already exists</h3><br> \n";
    } else {
        $block = new Vtiger_Block();
        $block->label = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
        $moduleInstance->addBlock($block);
    }

// Field Setup

    $field1 = Vtiger_Field::getInstance('claimitemsdetails_weightofitem', $moduleInstance);
    if (!$field1) {
        $field1 = new Vtiger_Field();
        $field1->name = 'claimitemsdetails_weightofitem';
        $field1->label = 'LBL_CLAIMITEMS_WEIGHTOFITEM';
        $field1->uitype = 1;
        $field1->table = 'vtiger_claimitems';
        $field1->column = $field1->name;
        $field1->summaryfield = 0;
        $field1->columntype = 'VARCHAR(255)';
        $field1->typeofdata = 'V~O';
        $block->addField($field1);
    }

    $field2 = Vtiger_Field::getInstance('claimitemsdetails_losscode', $moduleInstance);
    if (!$field2) {
        $field2 = new Vtiger_Field();
        $field2->name = 'claimitemsdetails_losscode';
        $field2->label = 'LBL_CLAIMITEMS_LOSSCODE';
        $field2->uitype = 15;
        $field2->table = 'vtiger_claimitems';
        $field2->column = $field2->name;
        $field2->summaryfield = 0;
        $field2->columntype = 'VARCHAR(255)';
        $field2->typeofdata = 'V~M';
        $block->addField($field2);
        $field2->setPicklistValues(array('Cosmetic', 'Structural', 'Loss of Components',
        'Missing', 'Water Damage', 'Inconvenience', 'Mechanical/Electrical', 'Environmental',
        'Reassembly', 'HHG Daily Allowance', 'HHG Expense Reimbursement', 'HHG Furniture Rental',
        'Auto Daily Allowance', 'Auto Arrangement of Rental', 'Yard/Grounds', 'Driveway/Parking Lot',
        'Gate', 'Other Physical Structure', 'Automobile'));
    }

    $field3 = Vtiger_Field::getInstance('claimitemsdetails_item', $moduleInstance);
    if (!$field3) {
        $field3 = new Vtiger_Field();
        $field3->name = 'claimitemsdetails_item';
        $field3->label = 'LBL_CLAIMITEMS_ITEM';
        $field3->uitype = 16;
        $field3->table = 'vtiger_claimitems';
        $field3->summaryfield = 1;
        $field3->column = $field3->name;
        $field3->columntype = 'VARCHAR(255)';
        $field3->typeofdata = 'V~M';
        $block->addField($field3);
        $field3->setPicklistValues(array(
        '1.5 - CP',
        '1.5 - PBO',
        '2 Wheel Dollies',
        '3.0 - CP',
        '3.0 - PBO',
        '4 Wheel Dollies',
        '4.5 - CP',
        '4.5 - PBO',
        '4x4 Vehicle',
        '6.0 - CP',
        '6.0 - PBO',
        '6.5 - CP',
        '6.5 - PBO',
        'A Frame Dollies',
        'Air Conditioner',
        'Airplanes, Gliders',
        'All Terrain Cycle',
        'Animal House',
        'Armoire',
        'Automobile',
        'Baker\'s Rack',
        'Bankers Box',
        'Bar, Portable',
        'Bar, Stool',
        'Barbells lbs.',
        'Basinette',
        'Bath',
        'Bath > 65 Cu Ft',
        'BBQ Grill, Large',
        'BBQ Grill, Small',
        'Bed, Bunk (Set 2)',
        'Bed, Footboard',
        'Bed, Full/Dbl.',
        'Bed, Headboard',
        'Bed, King',
        'Bed, Metal Frame',
        'Bed, Queen',
        'Bed, Rollaway',
        'Bed, Single',
        'Bed, Waterbed Base',
        'Bed, Youth',
        'Bench Fireside/Piano',
        'Bench, Harvest',
        'Bench, Vanity',
        'Bicycle',
        'Birdbath',
        'Boat Trailers',
        'Boats < 14 ft.',
        'Boats > 14 ft.',
        'Book Carton - CP',
        'Book Carton - PBO',
        'Book Carts',
        'Bookcase',
        'Bookshelf, Large',
        'Bookshelf, Small',
        'Bowling Bag / Ball',
        'Buffet',
        'Bulky Article > 400 lbs',
        'Cabinet, China',
        'Cabinet, Corner',
        'Cabinet, Curio',
        'Cabinet, Filing 2D',
        'Cabinet, Filing 4D',
        'Cabinet, Kitchen',
        'Cabinet, Metal',
        'Cabinet, Utility',
        'Camper (Truckless)',
        'Camper Shell',
        'Camper Trailers',
        'Canoe < 14 ft.',
        'Canoe > 14 ft.',
        'CD Rack',
        'Chair, Aluminum',
        'Chair, Arm',
        'Chair, Boudoir',
        'Chair, Child\'s',
        'Chair, Dining',
        'Chair, Folding',
        'Chair, High',
        'Chair, Kitchenette',
        'Chair, Lounge',
        'Chair, Metal',
        'Chair, Occasional',
        'Chair, Overstuffed',
        'Chair, Plastic',
        'Chair, Rocker',
        'Chair, Straight',
        'Chair, Wood',
        'Chair/Card Table Fold.',
        'Chairs',
        'Chaise, Aluminum',
        'Chaise, Metal',
        'Chaise, Plastic',
        'Chaise, Wood',
        'Chest',
        'Chest, Bachelor',
        'Chest, Cedar',
        'Coat Rack, Large',
        'Coat Rack, Small',
        'Computer/Printer',
        'Conf. Table',
        'Cooler',
        'Copier',
        'Cot, Folding',
        'Credenza',
        'Crib',
        'Crib Matt. - CP',
        'Crib Matt. - PBO',
        'Cushion',
        'Dbl. Matt. - CP',
        'Dbl. Matt. - PBO',
        'Deck Plate',
        'Dehumidifier',
        'Desk, Computer',
        'Desk, Credenza',
        'Desk, Office',
        'Desk, Secretary',
        'Desk, SM/Winthrop',
        'Desks w/Return',
        'Dinghy < 14 ft.',
        'Dinghy > 14 ft.',
        'Dish Pack - CP',
        'Dish Pack - PBO',
        'Dishwasher',
        'Doll House',
        'Drafting Table',
        'Dresser, Double',
        'Dresser, Mirror',
        'Dresser, Single',
        'Dresser, Triple',
        'Dresser, Vanity',
        'Dryer',
        'End Table',
        'Entertainment Ctr.',
        'Exec Desks',
        'Exercise Machine',
        'Exercycle',
        'Fan',
        'Farm Equipment',
        'Farm Implement',
        'Farm Trailer',
        'Fax',
        'File 2dr Lateral',
        'File 2dr Vertical',
        'File 3-4dr Lateral',
        'File 5dr Lateral',
        'File 5dr Vertical',
        'File Cabinets',
        'Fireplace Equip.',
        'Fish Tank 1',
        'Fish Tank 2',
        'Fish Tank 3',
        'Flat Bed Truck',
        'Flat Files',
        'Folding Chair',
        'Folding Table',
        'Footlocker',
        'Footstool',
        'Fork Lift',
        'Freezer,  11 to 15 cf.',
        'Freezer, 10 cf or <',
        'Freezer, 16 cf or >',
        'Furniture 1',
        'Furniture 2',
        'Furniture 3',
        'Futon',
        'Game Table',
        'Garden Hose',
        'GF Clock Carton - CP',
        'GF Clock Carton - PBO',
        'Glass Top 1',
        'Glass Top 2',
        'Glass Top 3',
        'Glass Top 4',
        'Glass Top 5',
        'Glass Top 6',
        'Glass Top 7',
        'Glass Top 8',
        'Glass Top 9',
        'Glider',
        'Go-Cart',
        'Golf Cart',
        'Golf Clubs',
        'Grandfather Clock',
        'Hamper',
        'Heater, Gas/Electric',
        'Home Gym Equipment',
        'Horse Trailers',
        'Hot Tub',
        'Hot Tub > 65 Cu Ft',
        'Hutch, Top',
        'Hutch--Large',
        'Hutch--Small',
        'Ironing Board',
        'Jacuzzi',
        'Jacuzzi > 65 Cu Ft',
        'Jet Ski',
        'Jet Ski > 14 ft.',
        'K/Q Split  - CP',
        'K/Q Split  - PBO',
        'Kayak < 14 ft.',
        'Kayak > 14 ft.',
        'Kennel',
        'Ladder, 3\' Step',
        'Ladder, 8\' Metal',
        'Ladder, Extension',
        'Lamp Crt. - CP',
        'Lamp Crt. - PBO',
        'Lamp, Floor/Pole',
        'Large Table',
        'Large TV > 40',
        'Lawn Roller',
        'Lawn Spreader',
        'Letter Carton',
        'Light/Bulky',
        'Limousine',
        'Luggage',
        'Machine Carts',
        'Magazine Rack',
        'Map Case',
        'Marble 1',
        'Marble 2',
        'Marble 3',
        'Marble 4',
        'Marble 5',
        'Marble 6',
        'Marble 7',
        'Marble 8',
        'Marble 9',
        'Masonite - 6ft',
        'Mattress Cover - CP',
        'Mattress Cover - PBO',
        'Microwave Oven',
        'Mini Mobile Homes',
        'Mirror 1',
        'Mirror 2',
        'Mirror 3',
        'Mirror 4',
        'Mirror 5',
        'Mirror 6',
        'Mirror 7',
        'Mirror 8',
        'Mirror 9',
        'Mirror Crt. - CP',
        'Mirror Crt. - PBO',
        'Misc. Crate 1',
        'Misc. Crate 2',
        'Misc. Crate 3',
        'Misc. Crate 4',
        'Misc. Crate 5',
        'Misc. Crate 6',
        'Misc. Crate 7',
        'Misc. Crate 8',
        'Misc. Crate 9',
        'Miscellaneous',
        'Miscellaneous 10 Cu',
        'Miscellaneous 100 Cu',
        'Miscellaneous 15 Cu',
        'Miscellaneous 20 Cu',
        'Miscellaneous 25 Cu',
        'Miscellaneous 5 Cu',
        'Miscellaneous 50 Cu',
        'Motorbike',
        'Motorcycle',
        'Mower, Hand',
        'Mower, Power',
        'Music Cabinet',
        'O/S Furniture',
        'Office Machine - LG',
        'Office Machine - SM',
        'Office Tote Box - CP',
        'Office Tote Box - PBO',
        'Ottoman',
        'Outdoor Child Gym',
        'Outdoor Child Slide',
        'Outdoor Furniture',
        'Outdoor Swing',
        'Panel Cart',
        'PC',
        'Pet Carrier',
        'Piano',
        'Piano, Concert',
        'Piano, Grand',
        'Piano, Spinet',
        'Piano, Upright',
        'Piano,Baby Grand',
        'Pickup and Camper',
        'Pickup Truck',
        'Picnic Bench',
        'Picnic Table',
        'Picture 1',
        'Picture 2',
        'Picture 3',
        'Picture 4',
        'Picture 5',
        'Picture 6',
        'Picture 7',
        'Picture 8',
        'Picture 9',
        'Ping Pong Table',
        'Plant Stand',
        'Plant, Artificial',
        'Plasma TV 1',
        'Plasma TV 2',
        'Plasma TV 3',
        'Plastic Cont., Large',
        'Playhouse',
        'Playpen',
        'Pool Table Comp.',
        'Pool Table Slate',
        'Power Tool, Hand',
        'Power Tool, Stand',
        'Qn/Kn Matt. - CP',
        'Qn/Kn Matt. - PBO',
        'Ramp, Large',
        'Ramp, Small',
        'Range, 20 Wide',
        'Range, 30 Wide',
        'Range, 36 Wide',
        'Record Storage',
        'Refrig 11 cf. or >',
        'Refrig., 6 cf. or <',
        'Refrig., 7 to 10 cf.',
        'Reg. Desks',
        'Riding Mower < 25hp',
        'Rowboat < 14 ft.',
        'Rowboat > 14 ft.',
        'Rug, Lg. Roll / Pad',
        'Rug, Sm. Roll/ Pad',
        'Safe Jack',
        'Safes',
        'Sailboat > 14 ft.',
        'Satellite Dish',
        'Satellite, Small',
        'Sculls > 14 ft.',
        'Serving Cart',
        'Sewing Mach, Portable',
        'Sewing Mach., Console',
        'Sewing Mach.w/ Cabinet',
        'Shelf, Large',
        'Shelf, Metal',
        'Shelf, Small',
        'Shelving',
        'Sideboard',
        'Single Matt. - CP',
        'Single Matt. - PBO',
        'Skiff < 14 ft.',
        'Skiff > 14 ft.',
        'Skis',
        'Sled',
        'Small Table',
        'Snow Mobile',
        'Snowblower',
        'Snowboard',
        'Sofa, 3 Cushion',
        'Sofa, 4 Cushion',
        'Sofa, Hide',
        'Sofa, Loveseat',
        'Sofa, Sec.',
        'Sofa, Wicker',
        'Spa',
        'Spa > 65 Cu Ft',
        'Speaker',
        'Speaker Stand',
        'Statue 1',
        'Statue 2',
        'Statue 3',
        'Stereo Rack',
        'Stool, Bar',
        'Storage Cabinet',
        'Suitcase',
        'Swing',
        'Table, Card',
        'Table, Changing',
        'Table, Childs',
        'Table, Coffee / End',
        'Table, Dining',
        'Table, Drafting',
        'Table, Drop / Occas',
        'Table, Kitchenette',
        'Table, Large Outdoor',
        'Table, Leaf',
        'Table, Microwave',
        'Table, Night',
        'Table, Small Outdoor',
        'Table, Utility',
        'Tea Cart',
        'Tech Benches',
        'Tire',
        'Tire w/ Rim',
        'Tool Shed',
        'Toolchest, Large',
        'Toolchest, Medium',
        'Toolchest, Small',
        'Tote Box',
        'Tote, Plastic Large',
        'Tote, Plastic Medium',
        'Tote, Plastic Small',
        'Toy Chest',
        'Tractor < 25hp',
        'Tractor > 25hp',
        'Trailer < 14 ft.',
        'Trailer >14 ft.',
        'Trampoline',
        'Transfer File',
        'Trash Can',
        'Trash Can, Large',
        'Trash Can, Small',
        'Tricycle',
        'Tri-Wall Tubs',
        'Trunk',
        'Tube',
        'TV, 19',
        'TV, 25-27',
        'TV, 32-36',
        'TV, Console',
        'TV, Portable',
        'TV/Radio Dish',
        'Twin Matt. - CP',
        'Twin Matt. - PBO',
        'Umbrella',
        'Utility Shed',
        'Utility Truck',
        'Vacuum Cleaner',
        'Van',
        'Wagon',
        'Wardrobe - CP',
        'Wardrobe - PBO',
        'Wardrobe, Large',
        'Wardrobe, Small',
        'Washing Machine',
        'Water Cooler',
        'Wheelbarrow',
        'Whirlpool Bath',
        'Whirlpool Bath > 65 Cu',
        'WhiteBoard',
        'Windsurfer',
        'Windsurfer > 14 ft.',
        'Work Bench',
        'TV-Flat 30-59 - CP',
        'TV-Flat 30-59 - PBO',
        'Wd/Speedpack - CP',
        'Wd/Speedpack - PBO',
        'Tote Box - CP',
        'Tote Box - PBO',
        'Small Mirror - CP',
        'Small Mirror - PBO',
        'Wardrobe, Flat - CP',
        'Wardrobe, Flat - PBO')
    );
    }



    $field4 = Vtiger_Field::getInstance('claimitemsdetails_natureofclaim', $moduleInstance);
    if (!$field4) {
        $field4 = new Vtiger_Field();
        $field4->name = 'claimitemsdetails_natureofclaim';
        $field4->label = 'LBL_CLAIMITEMS_NATUREOFCLAIM';
        $field4->uitype = 19;
        $field4->table = 'vtiger_claimitems';
        $field4->column = $field4->name;
        $field4->summaryfield = 0;
        $field4->columntype = 'TEXT';
        $field4->typeofdata = 'V~M';
        $block->addField($field4);
    }



    $field5 = Vtiger_Field::getInstance('claimitemsdetails_cartondamage', $moduleInstance);
    if (!$field5) {
        $field5 = new Vtiger_Field();
        $field5->name = 'claimitemsdetails_cartondamage';
        $field5->label = 'LBL_CLAIMITEMS_CARTONDAMGE';
        $field5->uitype = 15;
        $field5->table = 'vtiger_claimitems';
        $field5->column = $field5->name;
        $field5->summaryfield = 0;
        $field5->columntype = 'VARCHAR(255)';
        $field5->typeofdata = 'V~O';
        $block->addField($field5);
        $field5->setPicklistValues(array('Yes', 'No', 'N/A'));
    }



    $field6 = Vtiger_Field::getInstance('claimitemsdetails_yearpurchased', $moduleInstance);
    if (!$field6) {
        $field6 = new Vtiger_Field();
        $field6->name = 'claimitemsdetails_yearpurchased';
        $field6->label = 'LBL_CLAIMITEMS_YEARPURCHASED';
        $field6->uitype = 1;
        $field6->table = 'vtiger_claimitems';
        $field6->column = $field6->name;
        $field6->summaryfield = 0;
        $field6->columntype = 'VARCHAR(10)';
        $field6->typeofdata = 'V~O';
        $block->addField($field6);
    }



    $field7 = Vtiger_Field::getInstance('claimitemsdetails_originalcost', $moduleInstance);
    if (!$field7) {
        $field7 = new Vtiger_Field();
        $field7->name = 'claimitemsdetails_originalcost';
        $field7->label = 'LBL_CLAIMITEMS_ORIGINALCOST';
        $field7->uitype = 7;
        $field7->table = 'vtiger_claimitems';
        $field7->summaryfield = 1;
        $field7->column = $field7->name;
        $field7->columntype = 'DECIMAL(10,2)';
        $field7->typeofdata = 'NN~O';
        $block->addField($field7);
    }

    $field8 = Vtiger_Field::getInstance('claimitemsdetails_replacementcost', $moduleInstance);
    if (!$field8) {
        $field8 = new Vtiger_Field();
        $field8->name = 'claimitemsdetails_replacementcost';
        $field8->label = 'LBL_CLAIMITEMS_REPLACEMENTCOST';
        $field8->uitype = 7;
        $field8->table = 'vtiger_claimitems';
        $field8->summaryfield = 1;
        $field8->column = $field8->name;
        $field8->columntype = 'DECIMAL(10,2)';
        $field8->typeofdata = 'NN~O';
        $block->addField($field8);
    }

    $field9 = Vtiger_Field::getInstance('claimitemsdetails_claimantrequest', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_claimantrequest';
        $field9->label = 'LBL_CLAIMITEMS_CLAIMANTREQUEST';
        $field9->uitype = 15;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 1;
        $field9->column = $field9->name;
        $field9->columntype = 'VARCHAR(50)';
        $field9->typeofdata = 'V~M';
        $block->addField($field9);
        $field9->setPicklistValues(array('Cash', 'Repair', 'Unknown'));
    }

    $field10 = Vtiger_Field::getInstance('claimitemsdetails_amount', $moduleInstance);
    if (!$field10) {
        $field10 = new Vtiger_Field();
        $field10->name = 'claimitemsdetails_amount';
        $field10->label = 'LBL_CLAIMITEMS_AMOUNT';
        $field10->uitype = 7;
        $field10->table = 'vtiger_claimitems';
        $field10->summaryfield = 1;
        $field10->column = $field10->name;
        $field10->columntype = 'DECIMAL(10,2)';
        $field10->typeofdata = 'NN~O';
        $block->addField($field10);
    }

    $field11 = Vtiger_Field::getInstance('claimitemsdetails_originalconditions', $moduleInstance);
    if (!$field11) {
        $field11 = new Vtiger_Field();
        $field11->name = 'claimitemsdetails_originalconditions';
        $field11->label = 'LBL_CLAIMITEMS_ORGINALCONDITIONS';
        $field11->uitype = 1;
        $field11->table = 'vtiger_claimitems';
        $field11->summaryfield = 0;
        $field11->column = $field11->name;
        $field11->columntype = 'VARCHAR(100)';
        $field11->typeofdata = 'V~O';
        $block->addField($field11);
    }

    $field12 = Vtiger_Field::getInstance('claimitemsdetails_exceptions', $moduleInstance);
    if (!$field12) {
        $field12 = new Vtiger_Field();
        $field12->name = 'claimitemsdetails_exceptions';
        $field12->label = 'LBL_CLAIMITEMS_EXCEPTIONS';
        $field12->uitype = 33;
        $field12->table = 'vtiger_claimitems';
        $field12->summaryfield = 1;
        $field12->column = $field12->name;
        $field12->columntype = 'VARCHAR(100)';
        $field12->typeofdata = 'V~O';
        $block->addField($field12);
        $field12->setPicklistValues(array('Bent', 'Broken', 'Burned', 'Chipped', 'Dented', 'Faded', 'Gouged', 'Loose', 'Marred', 'Mildew', 'Motheaten', 'Peeling', 'Rubbed', 'Rusted', 'Scratched', 'Short', 'Soiled', 'Stained', 'Stretched', 'Torn', 'Badly Worn', 'Cracked', 'Crushed', 'Arm', 'Bottom', 'Corner', 'Front', 'Left', 'Leg', 'Rear', 'Right', 'Side', 'Top', 'Veneer', 'Edge', 'Center', 'Inside', 'Seat', 'Drawer', 'Door', 'Shelf', 'Hardware'));
    }

    $field13 = Vtiger_Field::getInstance('claimitemsdetails_datetaken', $moduleInstance);
    if (!$field13) {
        $field13 = new Vtiger_Field();
        $field13->name = 'claimitemsdetails_datetaken';
        $field13->label = 'LBL_CLAIMITEMS_DATETAKEN';
        $field13->uitype = 5;
        $field13->table = 'vtiger_claimitems';
        $field13->summaryfield = 1;
        $field13->column = $field13->name;
        $field13->columntype = 'DATE';
        $field13->typeofdata = 'D~O';
        $block->addField($field13);
    }

    $block = Vtiger_Block::getInstance('LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION', $moduleInstance);
    if ($block) {
        echo "<h3>The LBL_CLAIMITEMS_INFORMATION block already exists</h3><br> \n";
    }

    $field1 = Vtiger_Field::getInstance('claimitemsdetails_documented', $moduleInstance);
    if (!$field1) {
        $field1 = new Vtiger_Field();
        $field1->name = 'claimitemsdetails_documented';
        $field1->label = 'LBL_CLAIMITEMS_DOCUMENTED';
        $field1->uitype = 15;
        $field1->table = 'vtiger_claimitems';
        $field1->summaryfield = 0;
        $field1->column = $field1->name;
        $field1->columntype = 'VARCHAR(255)';
        $field1->typeofdata = 'V~O';
        $field1->setPicklistValues(array('Yes', 'No'));
        $block->addField($field1);
    }

    $field2 = Vtiger_Field::getInstance('claimitemsdetails_year', $moduleInstance);
    if (!$field2) {
        $field2 = new Vtiger_Field();
        $field2->name = 'claimitemsdetails_year';
        $field2->label = 'LBL_CLAIMITEMS_YEAR';
        $field2->uitype = 7;
        $field2->table = 'vtiger_claimitems';
        $field2->summaryfield = 0;
        $field2->column = $field2->name;
        $field2->columntype = 'INT(5)';
        $field2->typeofdata = 'I~O';
        $block->addField($field2);
    }

    $field3 = Vtiger_Field::getInstance('claimitemsdetails_make', $moduleInstance);
    if (!$field3) {
        $field3 = new Vtiger_Field();
        $field3->name = 'claimitemsdetails_make';
        $field3->label = 'LBL_CLAIMITEMS_MAKE';
        $field3->uitype = 2;
        $field3->table = 'vtiger_claimitems';
        $field3->summaryfield = 0;
        $field3->column = $field3->name;
        $field3->columntype = 'VARCHAR(255)';
        $field3->typeofdata = 'V~O';
        $block->addField($field3);
    }

    $field4 = Vtiger_Field::getInstance('claimitemsdetails_model', $moduleInstance);
    if (!$field4) {
        $field4 = new Vtiger_Field();
        $field4->name = 'claimitemsdetails_model';
        $field4->label = 'LBL_CLAIMITEMS_MODEL';
        $field4->uitype = 2;
        $field4->table = 'vtiger_claimitems';
        $field4->summaryfield = 0;
        $field4->column = $field4->name;
        $field4->columntype = 'VARCHAR(255)';
        $field4->typeofdata = 'V~O';
        $block->addField($field4);
    }

    $field5 = Vtiger_Field::getInstance('claimitemsdetails_contactname', $moduleInstance);
    if (!$field5) {
        $field5 = new Vtiger_Field();
        $field5->name = 'claimitemsdetails_contactname';
        $field5->label = 'LBL_CLAIMITEMS_CONTACTNAME';
        $field5->uitype = 2;
        $field5->table = 'vtiger_claimitems';
        $field5->summaryfield = 0;
        $field5->column = $field5->name;
        $field5->columntype = 'VARCHAR(255)';
        $field5->typeofdata = 'V~O';
        $block->addField($field5);
    }

    $field6 = Vtiger_Field::getInstance('claimitemsdetails_contactphone', $moduleInstance);
    if (!$field6) {
        $field6 = new Vtiger_Field();
        $field6->name = 'claimitemsdetails_contactphone';
        $field6->label = 'LBL_CLAIMITEMS_CONTACTPHONE';
        $field6->uitype = 11;
        $field6->table = 'vtiger_claimitems';
        $field6->column = $field6->name;
        $field6->columntype = 'VARCHAR(50)';
        $field6->typeofdata = 'V~O';
        $block->addField($field6);
    }

    $field7 = Vtiger_Field::getInstance('claimitemsdetails_contactcelltphone', $moduleInstance);
    if (!$field7) {
        $field7 = new Vtiger_Field();
        $field7->name = 'claimitemsdetails_contactcelltphone';
        $field7->label = 'LBL_CLAIMITEMS_CONTACTCELLPHONE';
        $field7->uitype = 11;
        $field7->table = 'vtiger_claimitems';
        $field7->column = $field7->name;
        $field7->columntype = 'VARCHAR(50)';
        $field7->typeofdata = 'V~O';
        $block->addField($field7);
    }

    $field8 = Vtiger_Field::getInstance('claimitemsdetails_contactemail', $moduleInstance);
    if (!$field8) {
        $field8 = new Vtiger_Field();
        $field8->name = 'claimitemsdetails_contactemail';
        $field8->label = 'LBL_CLAIMITEMS_CONTACTEMAIL';
        $field8->uitype = 13;
        $field8->table = 'vtiger_claimitems';
        $field8->column = $field8->name;
        $field8->columntype = 'VARCHAR(255)';
        $field8->typeofdata = 'V~O';
        $block->addField($field8);
    }

    $field9 = Vtiger_Field::getInstance('claimitemsdetails_location', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_location';
        $field9->label = 'LBL_CLAIMITEMS_LOCATION';
        $field9->uitype = 16;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 0;
        $field9->column = $field9->name;
        $field9->columntype = 'VARCHAR(255)';
        $field9->typeofdata = 'V~O';
        $field9->setPicklistValues(array('Origin', 'Destination', 'Extra Stop'));
        $block->addField($field9);
    }

    $field10 = Vtiger_Field::getInstance('claimitemsdetails_dateofincident', $moduleInstance);
    if (!$field10) {
        $field10 = new Vtiger_Field();
        $field10->name = 'claimitemsdetails_dateofincident';
        $field10->label = 'LBL_CLAIMITEMS_DATEOFINCIDENT';
        $field10->uitype = 5;
        $field10->table = 'vtiger_claimitems';
        $field10->summaryfield = 0;
        $field10->column = $field10->name;
        $field10->columntype = 'DATE';
        $field10->typeofdata = 'D~O';
        $block->addField($field10);
    }

    $field11 = Vtiger_Field::getInstance('claimitemsdetails_descriptiondamage', $moduleInstance);
    if (!$field11) {
        $field11 = new Vtiger_Field();
        $field11->name = 'claimitemsdetails_descriptiondamage';
        $field11->label = 'LBL_CLAIMITEMS_DESCRIPTIONDAMAGE';
        $field11->uitype = 19;
        $field11->table = 'vtiger_claimitems';
        $field11->column = $field11->name;
        $field11->columntype = 'TEXT';
        $field11->typeofdata = 'V~O';
        $block->addField($field11);
    }

    $block1 = Vtiger_Block::getInstance('LBL_ORIGINAL_COND_INFORMATION', $moduleInstance);
    if ($block1) {
        echo "<h3>The LBL_ORIGINAL_COND_INFORMATION block already exists</h3><br> \n";
    } else {
        $block1 = new Vtiger_Block();
        $block1->label = 'LBL_ORIGINAL_COND_INFORMATION';
        $moduleInstance->addBlock($block1);
    }

    $field9 = Vtiger_Field::getInstance('claimitemsdetails_existingfloortype', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_existingfloortype';
        $field9->label = 'LBL_CLAIMITEMS_EXISTING_COND_FLOOR';
        $field9->uitype = 16;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 0;
        $field9->column = $field9->name;
        $field9->columntype = 'VARCHAR(255)';
        $field9->typeofdata = 'V~O';
        $block1->addField($field9);
        $field9->setPicklistValues(array(
        'Carpet',
        'Hardwood',
        'Laminate Hardwood',
        'Laminate',
        'Tile',
        'Other',
    ));
    }

    $field9 = Vtiger_Field::getInstance('claimitemsdetails_existingroom', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_existingroom';
        $field9->label = 'LBL_CLAIMITEMS_EXISTING_COND_ROOM';
        $field9->uitype = 16;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 0;
        $field9->column = $field9->name;
        $field9->columntype = 'VARCHAR(255)';
        $field9->typeofdata = 'V~O';
        $block1->addField($field9);
        $field9->setPicklistValues(array(
        'Air',
        'Attic',
        'Bathroom',
        'Bedroom 2',
        'Bedroom 3',
        'Bedroom 4',
        'Bedroom 5',
        'Bulky Items',
        'Carton',
        'Crates',
        'Den',
        'Dining Room',
        'Equipment',
        'Extra Stop',
        'Extra Stop 2',
        'Extra Stop 3',
        'Family Room',
        'Garage',
        'Guest House',
        'Hallway',
        'Items/Furniture',
        'Kitchen',
        'Land',
        'Living Room',
        'Master Bedroom ',
        'Mini Storage',
        'Miscellaneous',
        'Nursery',
        'Office',
        'Porch/Outdoor',
        'Sea',
        'SIT',
        'Utility',
        'Workshop/Basement',
    ));
    }

    $field9 = Vtiger_Field::getInstance('claimitemsdetails_existingnotes', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_existingnotes';
        $field9->label = 'LBL_CLAIMITEMS_EXISTING_NOTES';
        $field9->uitype = 19;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 0;
        $field9->column = $field9->name;
        $field9->columntype = 'TEXT';
        $field9->typeofdata = 'V~O';
        $block1->addField($field9);
    }

    $block2 = Vtiger_Block::getInstance('LBL_ORIGINAL_FINAL_WALKTHROUGH', $moduleInstance);
    if ($block2) {
        echo "<h3>The LBL_ORIGINAL_FINAL_WALKTHROUGH block already exists</h3><br> \n";
    } else {
        $block2 = new Vtiger_Block();
        $block2->label = 'LBL_ORIGINAL_FINAL_WALKTHROUGH';
        $moduleInstance->addBlock($block2);
    }


    $field9 = Vtiger_Field::getInstance('claimitemsdetails_finalfloortype', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_finalfloortype';
        $field9->label = 'LBL_CLAIMITEMS_FINALWALK_COND_FLOOR';
        $field9->uitype = 16;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 0;
        $field9->column = $field9->name;
        $field9->columntype = 'VARCHAR(255)';
        $field9->typeofdata = 'V~O';
        $block2->addField($field9);
        $field9->setPicklistValues(array(
        'Carpet',
        'Hardwood',
        'Laminate Hardwood',
        'Laminate',
        'Tile',
        'Other',
    ));
    }

    $field9 = Vtiger_Field::getInstance('claimitemsdetails_finalroom', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_finalroom';
        $field9->label = 'LBL_CLAIMITEMS_FINAL_WALK_ROOM';
        $field9->uitype = 16;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 0;
        $field9->column = $field9->name;
        $field9->columntype = 'VARCHAR(255)';
        $field9->typeofdata = 'V~O';
        $block2->addField($field9);
        $field9->setPicklistValues(array(
        'Air',
        'Attic',
        'Bathroom',
        'Bedroom 2',
        'Bedroom 3',
        'Bedroom 4',
        'Bedroom 5',
        'Bulky Items',
        'Carton',
        'Crates',
        'Den',
        'Dining Room',
        'Equipment',
        'Extra Stop',
        'Extra Stop 2',
        'Extra Stop 3',
        'Family Room',
        'Garage',
        'Guest House',
        'Hallway',
        'Items/Furniture',
        'Kitchen',
        'Land',
        'Living Room',
        'Master Bedroom ',
        'Mini Storage',
        'Miscellaneous',
        'Nursery',
        'Office',
        'Porch/Outdoor',
        'Sea',
        'SIT',
        'Utility',
        'Workshop/Basement',
    ));
    }

    $field9 = Vtiger_Field::getInstance('claimitemsdetails_finalnotes', $moduleInstance);
    if (!$field9) {
        $field9 = new Vtiger_Field();
        $field9->name = 'claimitemsdetails_finalnotes';
        $field9->label = 'LBL_CLAIMITEMS_FINAL_NOTES';
        $field9->uitype = 19;
        $field9->table = 'vtiger_claimitems';
        $field9->summaryfield = 0;
        $field9->column = $field9->name;
        $field9->columntype = 'TEXT';
        $field9->typeofdata = 'V~O';
        $block2->addField($field9);
    }
    
    //Fix Item status picklist values
    Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_item_status WHERE item_status NOT IN ('Pending','Closed','Allocated')");
    
    

    $field = Vtiger_Field::getInstance('carrier_exception', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    $moduleInstance = Vtiger_Module::getInstance('ClaimItems');

    $field = Vtiger_Field::getInstance('shipper_exception', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('claims_agents', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('claims_employees', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('paid_vendor', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('chargedback_contractors', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('chargedback_company', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('paid_claimant', $moduleInstance);
    if ($field) {
        $field->delete();
    }

    //Fix broken related list

    $claimsInstance = Vtiger_Module::getInstance('Claims');
    $claimsItems = Vtiger_Module::getInstance('ClaimItems');

    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_relatedlists SET name='get_dependents_list', label='Items Details' WHERE tabid='$claimsInstance->id' AND related_tabid='$claimsItems->id' ");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";