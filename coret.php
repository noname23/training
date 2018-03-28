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

        $sql2 = pg_query($this->conn,"select * from db_schema.nasabah where umur='$max'");
        $idtua = [];
        while($data = pg_fetch_object($sql2))
        {
            array_push($idtua,$data->id_nasabah);
        }
        $idt = implode(",",$idtua);
        echo '----------------------------------------------------------';
        echo '<br/> ID Tuanya '.$idt.'<br/>';
        echo '----------------------------------------------------------'.'<br/>';
        $this->getAverage($idm,$idt);
    }

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