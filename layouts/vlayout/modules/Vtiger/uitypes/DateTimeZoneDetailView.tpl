{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}
&nbsp;({vtranslate($RECORD->getDisplayValue($FIELD_NAME|cat:'_zone'), 'Users')})