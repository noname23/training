<?php

class nasabah{
    var $conn;

    function koneksi()
    {
        $conn = pg_connect("host=localhost port=5432 dbname=bank user=postgres password=123456");
        if(!$conn){
            die("tidak terkoneksi");
        }
        return $conn;
    }

    function getUmur()
    {
        $this->conn = $this->koneksi();
        $sql = pg_query($this->conn,'select * from db_schema.nasabah');

        $array = [];
        $umurtertua = [];
        $umurtermuda = [];
        while($data = pg_fetch_object($sql))
        {
            array_push($array,$data->umur);
        }
        foreach($array as $m)
        {
            if($m == (max($array)))
            {
                array_push($umurtertua,$m);
            }
            if($m == (min($array)))
            {
                array_push($umurtermuda,$m);
            }
        }
        $tua = max($umurtertua);
        $muda = min($umurtermuda);
        echo "Umur Termuda => ".$muda.' Tahun'.' <br/>';
        echo "Umur Tertua => ".$tua.' Tahun'.' <br/>';
        $this->getid($muda,$tua);

        return $tua;
    }

    function getid($muda,$tua)
    {
        /* --------------------------------------- Mencari ID Muda --------------------------------------- */

        $sql = pg_query($this->conn,"select * from db_schema.nasabah where umur='$muda' ");
        $array = [];
        $idmuda = [];
        while($data = pg_fetch_object($sql))
        {
            array_push($array,$data->id_nasabah);
        }
        foreach ($array as $i){
            if($i == is_array($array)){
                array_push($idmuda,$i);
            }
        }
        $idm = implode(",",$idmuda);
        $totalidm = count($idmuda);

        echo '----------------------------------------------------------'.'<p>';
        echo 'ID Mudanya '.$idm.'<br/>';
        echo 'Total ID Mudanya => '.$totalidm.' Orang'.'<br/>';

        /* --------------------------------------- Mencari ID Tua --------------------------------------- */

        $sql = pg_query($this->conn,"select * from db_schema.nasabah where umur='$tua'  ");
        $array = [];
        $idtua = [];
        while($data = pg_fetch_object($sql))
        {
            array_push($array,$data->id_nasabah);
        }
        foreach ($array as $i){
            if($i == is_array($array)){
                array_push($idtua,$i);
            }
        }
        $idt = implode(",",$idtua);
        $totalidt = count($idtua);
        echo '----------------------------------------------------------'.'<p>';
        echo 'ID Tuanya '.$idt.'<br/>';
        echo 'Total ID Tuanya => '.$totalidt.' Orang'.'<br/>';
        echo '----------------------------------------------------------'.'<p>';
        $this->getSaldoMin($idmuda,$idtua);
        $this->getSaldoMax($idmuda,$idtua);
    }

    function getSaldoMin($idmuda,$idtua)
    {
        /* --------------------------------------- Saldo Minimal ID Muda --------------------------------------- */

        $sql = pg_query($this->conn,"select * from db_schema.rekening WHERE id_nasabah IN (". implode(',',$idmuda) .") ORDER BY saldo asc ");
        $listmuda = [];
        while ($data = pg_fetch_array($sql))
        {
            array_push($listmuda,$data['saldo']);
        }
        $min_value_m = $listmuda[0];
        for($i=0;$i<count($listmuda) ;$i++){
            if($listmuda[$i] < $min_value_m){
                $min_value_m=$listmuda[$i];
            }
        }
        echo "Saldo Terkecil ID Muda => ".$this->rupiah($min_value_m).' <br/>';

        /* --------------------------------------- Saldo Minimal ID Tua --------------------------------------- */

        $sql1 = pg_query($this->conn,"select * from db_schema.rekening WHERE id_nasabah IN (". implode(',',$idtua) .") ORDER BY saldo asc ");
        $listtua = [];
        while ($data = pg_fetch_array($sql1))
        {
            array_push($listtua,$data['saldo']);
        }
        $min_value_t = $listtua[0];
        for($i=0;$i<count($listtua) ;$i++){
            if($listtua[$i] < $min_value_t){
                $min_value_t=$listtua[$i];
            }
        }
        echo "Saldo Terkecil ID Tua => ".$this->rupiah($min_value_t).' <br/>';

        /* --------------------------------------- Total Saldo Minimum ID Muda dan Tua --------------------------------------- */

        $total_min = $min_value_m + $min_value_t;
        echo "Total Saldo Minimum ID Muda dan Tua => ".$this->rupiah($total_min).' <br>';
        echo '----------------------------------------------------------'.'<p>';

    }

    function getSaldoMax($idmuda,$idtua)
    {
        /* --------------------------------------- Saldo Maksimum ID Muda --------------------------------------- */

        $sql = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (". implode(',',$idmuda) .")");
        $listmuda = [];
        while ($data = pg_fetch_array($sql))
        {
            array_push($listmuda,$data['saldo']);
        }
        $max_value_m = 0;
        for($i=0;$i<count($listmuda) ;$i++){
            if($listmuda[$i] > $max_value_m){
                $max_value_m=$listmuda[$i];
            }
        }
        echo "Saldo Terbesar ID Muda => ".$this->rupiah($max_value_m).' <br/>';

        /* --------------------------------------- Saldo Maksimum ID Tua --------------------------------------- */

        $sql1 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (". implode(',',$idtua) .")");
        $listtua = [];
        while ($data = pg_fetch_array($sql1))
        {
            array_push($listtua,$data['saldo']);
        }
        $max_value_t = 0;
        for($i=0;$i<count($listtua) ;$i++){
            if($listtua[$i] > $max_value_t){
                $max_value_t=$listtua[$i];
            }
        }
        echo "Saldo Terbesar ID Tua => ".$this->rupiah($max_value_t).' <br/>';

        /* --------------------------------------- Total Saldo Maksimum ID Muda dan Tua --------------------------------------- */

        $total_max = $max_value_m + $max_value_t;
        echo "Total Saldo Maksimum ID Muda dan Tua => ".$this->rupiah($total_max);
    }

    function rupiah($angka)
    {
        $hasil = number_format($angka,0,',','.');
        return $hasil;
    }

}
?>

<?php
$m = new nasabah;
$m->getUmur();
?>