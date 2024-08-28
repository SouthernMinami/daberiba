<?php
namespace Types;

enum ValueType: string
{
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case DATE = 'date'; // yyyy-mm-dd
}
