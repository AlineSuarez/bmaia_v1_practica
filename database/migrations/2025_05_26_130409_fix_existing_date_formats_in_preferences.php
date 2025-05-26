<?php
use Illuminate\Support\Facades\DB;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up()
    {
        DB::table('preferences')->where('date_format', 'dd/mm/yyyy')->update(['date_format' => 'DD/MM/YYYY']);
        DB::table('preferences')->where('date_format', 'mm/dd/yyyy')->update(['date_format' => 'MM/DD/YYYY']);
        DB::table('preferences')->where('date_format', 'yyyy-mm-dd')->update(['date_format' => 'YYYY-MM-DD']);
    }

    public function down()
    {
        DB::table('preferences')->where('date_format', 'DD/MM/YYYY')->update(['date_format' => 'dd/mm/yyyy']);
        DB::table('preferences')->where('date_format', 'MM/DD/YYYY')->update(['date_format' => 'mm/dd/yyyy']);
        DB::table('preferences')->where('date_format', 'YYYY-MM-DD')->update(['date_format' => 'yyyy-mm-dd']);
    }
};

