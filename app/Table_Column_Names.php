<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class Table_Column_Names  extends Model
{
  protected $table ="TABLE_COLUMN_NAMES";
    protected $guarded = array('_token');
  const CREATED_AT = 'CREATEDDATE';
  const UPDATED_AT = 'LASTUPDATEDATE';
    protected $primaryKey ="ID";
}
