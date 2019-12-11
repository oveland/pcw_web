<?php

use App\Models\BEA\DiscountType;
use App\Models\BEA\MarkDiscountType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddColumnUidToBeaDiscountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bea_discount_types', function (Blueprint $table) {
            $table->integer('uid')->nullable();
        });

        Schema::table('bea_mark_discount_types', function (Blueprint $table) {
            $table->integer('uid')->nullable();
        });

        $this->updateDiscountTypes(DiscountType::all());
        $this->updateDiscountTypes(MarkDiscountType::all());
    }

    public function updateDiscountTypes($discountTypes)
    {
        foreach ($discountTypes as $discountType){
            if( Str::contains(strtolower($discountType->name), "auxilio")  ){
                $discountType->uid = 1;
            }
            else if( Str::contains(strtolower($discountType->name), "combustible")  ){
                $discountType->uid = 2;
            }
            else if( Str::contains(strtolower($discountType->name), "operativos")  ){
                $discountType->uid = 3;
            }
            else if( Str::contains(strtolower($discountType->name), "peajes")  ){
                $discountType->uid = 4;
            }else{
                $discountType->uid = DiscountType::max('uid') + 1;
            }
            $discountType->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_discount_types', function (Blueprint $table) {
            $table->dropColumn('uid');
        });

        Schema::table('bea_mark_discount_types', function (Blueprint $table) {
            $table->dropColumn('uid');
        });
    }
}
