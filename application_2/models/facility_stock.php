<?php
class Facility_Stock extends Doctrine_Record {
		
	public function setTableDefinition()
	{
		$this -> hasColumn('facility_code', 'int'); 
		$this -> hasColumn('kemsa_code', 'varchar',20); 
	    $this -> hasColumn('batch_no', 'varchar',20); 
		$this -> hasColumn('manufacture', 'varchar',20); 
		$this -> hasColumn('expiry_date', 'date'); 
		$this -> hasColumn('balance', 'int'); 
		$this -> hasColumn('quantity', 'int'); 
		$this -> hasColumn('status', 'tinyint');
		$this -> hasColumn('stock_date', 'date');
		$this -> hasColumn('sheet_no', 'int',15);
		//$this -> hasColumn('warehouse', 'varchar',255);
		
	}

	public function setUp() {
		$this -> setTableName('Facility_Stock');		
		$this -> hasMany('drug as stock_Drugs', array('local' => 'kemsa_code', 'foreign' => 'Kemsa_Code'));
		$this -> hasMany('facilities as Coder', array('local' => 'facility_code', 'foreign' => 'facility_code'));
		$this -> hasMany('Drug as Code', array('local' => 'kemsa_code', 'foreign' => 'id'));
	}
	public static function getAllStock($facility)
		  {
			  $query = Doctrine_Query::create() -> select("DISTINCT kemsa_code") -> from("Facility_Stock")-> where("facility_code ='$facility' and expiry_date >= NOW()")->andwhere("status='1'")->andwhere("balance>0")->groupby("kemsa_code"); 
			  $search = $query ->execute();
			 return $search;
				
		  }
	
	 public static function getAll($desc,$facility_c)
		  {
			  $query = Doctrine_Query::create() -> select("* ") -> from("Facility_Stock")-> where("kemsa_code='$desc' AND expiry_date >= NOW()")
			  ->andwhere("facility_code ='$facility_c'")->andwhere("status='1'")->andwhere("balance>0")->orderby( "expiry_date ASC" ); ;
			  $search = $query -> execute();
			 return $search;
				
		  }
	public static function update_facility_stock($data_array){
		$o = new Facility_Stock ();
	    $o->fromArray($data_array);
		$o->save();		
		return TRUE;
	}
	public static function count_facility_stock($facility_code){
		$query = Doctrine_Query::create() -> select("kemsa_code,SUM(balance) AS quantity1") -> from(" facility_stock") -> where("facility_code=$facility_code AND
		status='1'")->groupby( "kemsa_code");
		$stocktake = $query ->execute();
		
		return $stocktake;
	}
//get if the facility has stock
public static function count_facility_stock_first($facility_code){
		$query = Doctrine_Query::create() -> select("kemsa_code,SUM(balance) AS quantity1") -> from(" facility_stock") -> where("facility_code=$facility_code")->groupby( "kemsa_code");
		$stocktake = $query ->execute();
		
		return $stocktake;
	}
	public static function count_all_facility_stock(){
		$query = Doctrine_Query::create() -> select("kemsa_code,SUM( quantity ) AS quantity1") -> from(" facility_stock") -> where("status='1'")
		->groupby( "kemsa_code");
		$stocktake = $query ->execute();	
		return $stocktake;
	}
	public static function count_all_county_stock($county){
/*SELECT fs.kemsa_code, SUM( fs.quantity ) as quantity1
FROM districts d, counties c, facilities f, facility_stock fs
WHERE c.id = d.county
AND d.id = f.district
AND c.id =1
AND fs.facility_code = f.facility_code
AND fs.STATUS =1
GROUP BY fs.kemsa_code*/
		$query = Doctrine_Query::create() -> select("facility_stock.kemsa_code, SUM( facility_stock.quantity ) as quantity1") 
		-> from("facility_stock , districts, counties, facilities ") 
		-> where("counties.id = districts.county")
		->andWhere("districts.id = facilities.district")
        ->andWhere("counties.id =$county")
        ->andWhere("facility_stock.facility_code = facilities.facility_code")
        ->andWhere("facility_stock.STATUS ='1'")
		->groupby( "facility_stock.kemsa_code");
		$stocktake = $query ->execute();	
		return $stocktake;
	}
		public static function getStockouts($date,$facility,$date1){
		//echo $date.'<br>',$facility.'<br>',$date1.'<br>';
		$query = Doctrine_Query::create() -> select("*") -> from("Facility_Stock")->where("facility_code='$facility' and expiry_date between '$date1' and '$date'");
		$stockouts = $query -> execute();
		return $stockouts;
	}
	public static function expiries($facility_c){
		$query = Doctrine_Query::create() -> select("*") -> from("Facility_Stock") -> where("facility_code='$facility_c' and expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)")->
		andwhere("status=1");
		
		$stocks= $query -> execute();
		return $stocks;
	}
	public static function expiries_count($facility_c){
		$query = Doctrine_Query::create() -> select("sum(balance) as balance") -> from("Facility_Stock") -> where("facility_code='$facility_c' and expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)");	
		$stocks= $query -> execute();
		return $stocks[0];
	}
	public static function getexp($date,$facility){
		$query=Doctrine_query::create()->select("*")->from("Facility_Stock")->where ("facility_code='$facility' and expiry_date<='$date'");
		$expire=$query->execute();
		return $expire;
	}
	public static function get_exp_count($date,$facility){
		$query=Doctrine_query::create()->select("batch_no")->from("Facility_Stock")
		->where ("facility_code='$facility' and expiry_date<='$date'")
		->andWhere("status='1'")->groupBy("batch_no");
		$expire=$query->count();
		return $expire;
	}
	public static function getAll1($facility)
		  {
	 
		$query = Doctrine_Query::create() -> select("facility_code,kemsa_code,SUM(quantity)") -> from(" facility_stock")->where("facility_code=$facility")->groupby( "kemsa_code");
		$stocktake = $query ->execute();
		
		return $stocktake;
				
		  }
		  //facility commodities
		  public static function get_commodities($facility){
		  	$query = Doctrine_Query::create() -> select("facility_code,kemsa_code,balance,SUM(balance)") -> from(" facility_stock")->where("facility_code='$facility'")->groupby( "kemsa_code");
			$stocktake = $query ->execute();
		
		return $stocktake;
		  }
		  public static function Allexpiries(){
		$query = Doctrine_Query::create() -> select("*") -> from("Facility_Stock") -> where("expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)");
		
		$stocks= $query -> execute();
		return $stocks;
	}
	
	public static function poo($drug_id){
               $query = Doctrine_Query::create() -> select("SUM(`balance`) as total,expiry_date") -> from("Facility_Stock") -> where("kemsa_code='$drug_id'") -> andwhere("expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 12 MONTH)")
               ->groupby("MONTH(expiry_date) ASC");
               
               $bubu= $query -> execute();
               return $bubu->toArray();
       }

      public static function disable_facility_stock($facility){
      	$q = Doctrine_Manager::getInstance()->getCurrentConnection();
		$q->execute("UPDATE facility_stock SET balance='0',status='0' where facility_code=$facility");
      	
      }
////////////// getting district stock level
     public function get_district_stock_level($distict){
     
$inserttransaction = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT d.drug_name, ceil(SUM( fs.balance / d.total_units )) AS total
FROM facility_stock fs, drug d, facilities f
WHERE fs.facility_code = f.facility_code
AND f.district =  '$distict'
AND d.id = fs.kemsa_code
GROUP BY fs.kemsa_code
ORDER BY d.drug_name ASC  ");

        return $inserttransaction ;}
   

        
////////////// getting facility stock level
     public function get_facility_stock_level($distict){
     
$inserttransaction = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT d.drug_name, ceil(SUM( fs.balance / d.total_units )) AS total
FROM facility_stock fs, drug d, facilities f
WHERE fs.facility_code = f.facility_code
AND f.facility_code =  '$distict'
AND d.id = fs.kemsa_code
GROUP BY fs.kemsa_code
ORDER BY d.drug_name ASC  ");

        return $inserttransaction ;}        
        
        
        /////getting cost of exipries 
            public function get_district_cost_of_exipries($distict){
     
$inserttransaction = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT date_format( fs.expiry_date, '%b' ) as cal_month , ceil(sum( (fs.balance/ d.total_units) * d.unit_cost )) AS total
FROM facility_stock fs, facilities f, drug d
WHERE fs.facility_code = f.facility_code
AND `expiry_date` <= NOW( )
AND f.district ='$distict'
AND d.id = fs.kemsa_code
GROUP BY month( expiry_date ) asc");
        return $inserttransaction ;
			} 
public static function get_decom_count($district)
	{
		$query=Doctrine_Query::create()-> 
        select("DISTINCT(facility_stock.facility_code)")->from("facility_stock,facilities,districts")
        ->where("facility_stock.facility_code=facilities.facility_code")->andWhere("facilities.district=districts.id")
		->andWhere("facility_stock.status=2")->andWhere("districts.id=$district");
		$order=$query->execute();
		$order=count($order);
		//echo $order; exit;
		return ($order);
		
	}
	
	////get maufacuture for a given batch no
	
	public static function get_batch_details($batch_no,$kemsa_code){
		$query = Doctrine_Query::create() -> select("*") -> from("Facility_Stock") -> where("kemsa_code='$kemsa_code' and batch_no='$batch_no'")->andwhere("status='1'");
		
		$stocks= $query -> execute();
		$stocks=$stocks->toArray();
		return $stocks[0];
	}
	
}