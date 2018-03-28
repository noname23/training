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
        $min = 1;
        $max = 100;
        while($data = pg_fetch_object($sql))
        {
            array_push($array,$data->umur);
        }
        for ($i=0;$i<count($array);$i++){
            if($array[$i] < $min){
                $min = $array[$i];
            }
            if ($array[$i] > $max){
                $max = $array[$i];
            }
        }
        echo 'Umur Ter-Muda => '.$min.' Tahun'.'<br/>';
        echo 'Umur Ter-Tua => '.$max.' Tahun'.'<br/>';
        $this->getID($min,$max);

        return $min;
    }

    function getID($min,$max)
    {
        $sql1 = pg_query($this->conn,"select * from db_schema.nasabah where umur='$min'");
        $idmuda = [];
        while($data = pg_fetch_object($sql1))
        {
            array_push($idmuda,$data->id_nasabah);
        }
        $idm = implode(",",$idmuda);
        echo '----------------------------------------------------------';
        echo '<br/> ID Mudanya '.$idm.'<br/>';
        echo 'Total ID Mudanya => '.count($idmuda).' Orang'.'<br/>';

        $sql2 = pg_query($this->conn,"select * from db_schema.nasabah where umur='$max'");
        $idtua = [];
        while($data = pg_fetch_object($sql2))
        {
            array_push($idtua,$data->id_nasabah);
        }
        $idt = implode(",",$idtua);
        echo '----------------------------------------------------------';
        echo '<br/> ID Tuanya '.$idt.'<br/>';
        echo 'Total ID Tuanya => '.count($idtua).' Orang'.'<br/>';
        echo '----------------------------------------------------------'.'<br/>';
        $this->getSaldoMin($idm,$idt);
        echo '----------------------------------------------------------'.'<br/>';
        $this->getSaldoMax($idm,$idt);
        echo '----------------------------------------------------------'.'<br/>';
//        $this->getRange($idm,$idt);
        $this->getAverage($idm,$idt);
    }

    function getSaldoMin($idm,$idt)
    {
        $sql1 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idm.")");
        $listmuda = [];
        while ($data = pg_fetch_array($sql1))
        {
            array_push($listmuda,$data['saldo']);
        }
        $min_value_m = $listmuda[0];
        for($i=0;$i<count($listmuda) ;$i++){
            if($listmuda[$i] < $min_value_m){
                $min_value_m = $listmuda[$i];
            }
        }
        echo "Saldo Terkecil ID Muda => ".$this->format($min_value_m).' <br/>';

        $sql2 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idt.")");
        $listtua = [];
        while ($data = pg_fetch_array($sql2))
        {
            array_push($listtua,$data['saldo']);
        }
        $min_value_t = $listtua[0];
        for($i=0;$i<count($listtua) ;$i++){
            if($listtua[$i] < $min_value_t){
                $min_value_t = $listtua[$i];
            }
        }
        echo "Saldo Terkecil ID Tua => ".$this->format($min_value_t).' <br/>';

        $totalmin = $min_value_m + $min_value_t;
        echo "Total Saldo Minimum ID Tua dan Muda => ".$this->format($totalmin).'<br/>';
    }

    function getSaldoMax($idm,$idt)
    {
        $sql1 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idm.")");
        $listmuda = [];
        while ($data = pg_fetch_array($sql1))
        {
            array_push($listmuda,$data['saldo']);
        }
        $max_value_m = 0;
        for($i=0;$i<count($listmuda) ;$i++){
            if($listmuda[$i] > $max_value_m){
                $max_value_m = $listmuda[$i];
            }
        }
        echo "Saldo Terbesar ID Muda => ".$this->format($max_value_m).' <br/>';

        $sql2 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idt.")");
        $listtua = [];
        while ($data = pg_fetch_array($sql2))
        {
            array_push($listtua,$data['saldo']);
        }
        $max_value_t = 0;
        for($i=0;$i<count($listtua) ;$i++){
            if($listtua[$i] > $max_value_t){
                $max_value_t = $listtua[$i];
            }
        }
        echo "Saldo Terbesar ID Tua => ".$this->format($max_value_t).' <br/>';

        $totalmax = $max_value_m + $max_value_t;
        echo "Total Saldo Maksimum ID Tua dan Muda => ".$this->format($totalmax).'</br>';
    }

    /*function getRange($idm,$idt){
        $sql1 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idm.") ORDER BY saldo ASC");
        $sql2 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idt.") ORDER BY saldo ASC");
        $listmuda = [];
        $rlist = [];
        while ($data = pg_fetch_array($sql1))
        {
            array_push($listmuda,$data['saldo']);
        }
        $min = 10000000;
        $max = 100000000;
        for($i=0;$i<count($listmuda);$i++){
            if($listmuda[$i] > $min && $listmuda[$i] < $max){
                array_push($rlist,$listmuda[$i]);
            }
        }
        echo "Range Saldo ID Muda 1jt s/d 100jt => ".implode(',',$rlist).'<br/>';
        echo "Jumlah Saldonya ".count($rlist).' Orang'.'<br/>';
        echo '----------------------------------------------------------'.'<br/>';

        $listtua = [];
        $rlist_t = [];
        while ($data = pg_fetch_array($sql2))
        {
            array_push($listtua,$data['saldo']);
        }
        $min = 10000000;
        $max = 100000000;
        for($i=0;$i<count($listtua);$i++){
            if($listtua[$i] > $min && $listtua[$i] < $max){
                array_push($rlist_t,$listtua[$i]);
            }
        }
        echo "Range Saldo ID Tua 10jt s/d 100jt => ".implode(',',$rlist_t).'<br/>';
        echo "Jumlah Saldonya ".count($rlist_t).' Orang'.'<br/>';
    }*/

    function getAverage($idm,$idt){
        $sql1 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idm.") ORDER BY saldo ASC");
        $listmuda = [];
        while ($data = pg_fetch_array($sql1))
        {
            array_push($listmuda,$data['saldo']);
        }
        $banyak = count($listmuda);
        $totalsaldo = array_sum($listmuda);
        $avgmuda = $totalsaldo/$banyak;
        echo "Rata-rata saldo dari ID Muda => ".$this->format($avgmuda).'<br>';

        $sql2 = pg_query($this->conn,"select saldo from db_schema.rekening WHERE id_nasabah IN (".$idt.") ORDER BY saldo ASC");
        $listtua = [];
        while ($data = pg_fetch_array($sql2))
        {
            array_push($listtua,$data['saldo']);
        }
        $banyak = count($listtua);
        $totalsaldo = array_sum($listtua);
        $avgtua = $totalsaldo/$banyak;
        echo "Rata-rata saldo dari ID Tua => ".$this->format($avgtua);
    }

    function format($a){
        $nominal = number_format($a,0,'.',',');
        return $nominal;
    }

}
?>

<?php
$m = new nasabah;
$m->getUmur();

?>