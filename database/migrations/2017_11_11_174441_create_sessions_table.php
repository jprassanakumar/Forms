<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        //storing Form name
        Schema::create('Forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('form_name');
            $table->dateTime ( 'updated_at' );
            $table->dateTime ( 'created_at' );
            $table->string ( 'updated_by' );
        });

        //Type of elements present in a form
        Schema::create('FormElements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('element');
            $table->dateTime ( 'updated_at' );
            $table->dateTime ( 'created_at' );
            $table->string ( 'updated_by' );
        });

        //Elements present in a form and there unqiue ids
         Schema::create('FormSkeleton', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned ();
            $table->integer('element_id')->unsigned ();
            $table->string('element_unqiue_id');
            $table->dateTime ( 'updated_at' );
            $table->dateTime ( 'created_at' );
            $table->string ( 'updated_by' );

            $table->foreign ( 'form_id' )->references ( 'id' )->on ( 'Forms' );
            $table->foreign ( 'element_id' )->references ( 'id' )->on ( 'FormElements' );
        });

         //Every time user add data an instance is created and id is used to store data
         Schema::create('FormLog', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned();
            
            $table->dateTime ( 'updated_at' );
            $table->dateTime ( 'created_at' );
            $table->string ( 'updated_by' );

             $table->foreign ( 'form_id' )->references ( 'id' )->on ( 'Forms' );
        });


    //Data is stored with respect to form unique ids     
    Schema::create('FormData', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_instance_id')->unsigned();
            $table->integer('element_skeleton_id')->unsigned();
            $table->string('data');
            $table->dateTime ( 'updated_at' );
            $table->dateTime ( 'created_at' );
            $table->string ( 'updated_by' );

            $table->foreign ( 'form_instance_id' )->references ( 'id' )->on ( 'FormLog' );
            $table->foreign ( 'element_skeleton_id' )->references ( 'id' )->on ( 'FormSkeleton' );
        });

 
        $types=array(
            ['id' =>1,'element'=>'button'],
            ['id' =>2,'element'=>'textbox'],
            ['id' =>3,'element'=>'date'],
            ['id' =>4,'element'=>'fileupload'],
            );
        DB::table('FormElements')->insert($types);



        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sessions');
    }
}
