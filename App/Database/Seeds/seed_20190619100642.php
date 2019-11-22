<?php
namespace App\Database\Seeds;
use Core\Database\Table;

class seed_20190619100642 {

    public function up(){
        $table = new Table();
        $table->table('m_users');
        $table->addSeed('Username', 'superadmin');
        $password = encryptMd5(get_variable()."superadminsuperadmin");
        $table->addSeed('Password', $password);
        $table->seeds();

    }
}