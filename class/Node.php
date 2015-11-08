<?php

include("NodeFlags.php");
include("NodeStatistics.php");
include("NodeInfo.php");

class Node
{

    private $firstseen;
    private $lastseen;
    private $flag;
    private $statistics;
    private $nodeinfo;

   

    public function fillRRDData(){
        if(!$this->checkRRDFileExists()){
            $this->createRRDFile();
        }
        $data = Array();
        $data[] = time();
        
        //TODO: Memory Usage
        $memoryUsage = $this->getStatistics()->getMemoryUsage() * 100;
        $data[] = $memoryUsage;
        
        //$data[] = time();
        //TODO: clients
        $clients = $this->getStatistics()->getClients();
        $data[] = $clients;
        //TODO: rootfs_usage
        $rootfs = $this->getStatistics()->getRootfsUsage() * 100;
        $data[] = $rootfs;
        //TODO: loadavg
        $loadavg = $this->getStatistics()->getLoadavg();
        $data[] = $loadavg;
        //TODO: traffic
        $trafficMgmtRxbytes = $this->getStatistics()->getTraffic()->getMgmtRx()->getBytes();
        $data[] = $trafficMgmtRxbytes;
        $trafficMgmtRxpackets = $this->getStatistics()->getTraffic()->getMgmtRx()->getPackets();
        $data[] = $trafficMgmtRxpackets;
        $trafficMgmtTxbytes = $this->getStatistics()->getTraffic()->getMgmtTx()->getBytes();
        $data[] = $trafficMgmtTxbytes;
        $trafficMgmtTxpackets = $this->getStatistics()->getTraffic()->getMgmtTx()->getPackets();
        $data[] = $trafficMgmtTxpackets;
        $trafficRxbytes = $this->getStatistics()->getTraffic()->getRx()->getBytes();
        $data[] = $trafficRxbytes;
        $trafficRxpackets = $this->getStatistics()->getTraffic()->getRx()->getPackets();
        $data[] = $trafficRxpackets;
        $trafficTxbytes = $this->getStatistics()->getTraffic()->getTx()->getBytes();
        $data[] = $trafficTxbytes;
        $trafficTxpackets = $this->getStatistics()->getTraffic()->getTx()->getPackets();
        $data[] = $trafficTxpackets;
        $trafficForwardedBytes = $this->getStatistics()->getTraffic()->getForward()->getBytes();
        $data[] = $trafficForwardedBytes;
        $trafficForwardedPackets = $this->getStatistics()->getTraffic()->getForward()->getPackets();
        $data[] = $trafficForwardedPackets;
        
       
        $string = implode(":",$data);
        
        $ret = rrd_update($this->getRRDFileName(), array($string));
    }

    private function checkRRDFileExists(){
        return file_exists($this->getRRDFileName());
    }

    private function createRRDFile(){
        $options = array(
            "--step", "60",            // Use a step-size of 5 minutes
            "DS:memoryUsage:GAUGE:600:0:100",
            "DS:clients:GAUGE:600:0:U",
            "DS:rootfsUsage:GAUGE:600:0:100",
            "DS:loadavg:GAUGE:600:0:U",
            "DS:trafMgmtRxBy:GAUGE:600:0:U",
            "DS:trafMgmtRxPa:GAUGE:600:0:U",
            "DS:trafMgmtTxBy:GAUGE:600:0:U",
            "DS:trafMgmtTxPa:GAUGE:600:0:U",            
            "DS:trafRxBy:GAUGE:600:0:U",
            "DS:trafRxPa:GAUGE:600:0:U",            
            "DS:trafTxBy:GAUGE:600:0:U",
            "DS:trafTxPa:GAUGE:600:0:U",            
            "DS:trafForwardBy:GAUGE:600:0:U",
            "DS:trafForwardPa:GAUGE:600:0:U",
            "RRA:AVERAGE:0.5:1:288",
            "RRA:AVERAGE:0.5:12:168",
            "RRA:AVERAGE:0.5:228:365",
        );

        $ret = rrd_create($this->getRRDFileName(), $options);
        echo rrd_error();
    }

    public function getRRDFileName(){
        return dirname(__FILE__)."/../rrdData/nodes/".$this->nodeinfo->getNodeId().".rrd";
    }
    
    public function getFileName(){
        return dirname(__FILE__)."/../graphs/nodes/".$this->nodeinfo->getNodeId().".png";
    }

    public function makeGraph($width, $height){
        echo "make Graph <br>";
        $this->createGraph("-1h","Node: ".$this->nodeinfo->getNodeId(), $width, $height);
    }
    
    private function createGraph($start, $title, $width, $height) {
        $options = array(
            "--slope-mode",
            "--start", $start,
            "--title=$title",
            "--vertical-label=Clients",
            "--width",$width,
            "--height",$height,
            "--lower=0",
            "DEF:clients=".$this->getRRDFileName().":clients:AVERAGE",           
            "AREA:clients#00FF00:Clients online",
        );
        echo "ok";
        echo "<br>";
        $ret = rrd_graph($this->getFileName(),$options);
        echo rrd_error();
        echo "<br>";
    }

     
    /**
     * @return mixed
     */
    public function getFirstseen()
    {
        return $this->firstseen;
    }

    /**
     * @param mixed $firstseen
     */
    public function setFirstseen($firstseen)
    {
        $this->firstseen = $firstseen;
    }

    /**
     * @return mixed
     */
    public function getLastseen()
    {
        return $this->lastseen;
    }

    /**
     * @param mixed $lastseen
     */
    public function setLastseen($lastseen)
    {
        $this->lastseen = $lastseen;
    }

    /**
     * @return mixed
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param mixed $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * @return mixed
     */
    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     * @param mixed $statistics
     */
    public function setStatistics($statistics)
    {
        $this->statistics = $statistics;
    }

    /**
     * @return mixed
     */
    public function getNodeinfo()
    {
        return $this->nodeinfo;
    }

    /**
     * @param mixed $nodeinfo
     */
    public function setNodeinfo($nodeinfo)
    {
        $this->nodeinfo = $nodeinfo;
    }


}