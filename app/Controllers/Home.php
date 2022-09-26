<?php

namespace App\Controllers;

class Home extends BaseController
{
   
    public function index()
    {
        return view('welcome_message');
    }

    public function area()
    {
        $db = \Config\Database::connect();

        $storeArea = $db->table('store_area')->get()->getResult();

        return $this->response->setStatusCode(200)->setJson($storeArea);
    }

    public function chart(){

        $area = $this->request->getVar('area');
        $dateFrom = $this->request->getVar('dateFrom');
        $dateTo = $this->request->getVar('dateTo');

        $db = \Config\Database::connect();

        $sql = 'SELECT 
        area_name, 
        sum(compliance)/count(compliance) * 100 AS value
        FROM report_product 
        JOIN store ON report_product.store_id = store.store_id 
        JOIN store_area ON store_area.area_id = store.area_id 
        WHERE tanggal BETWEEN ? AND ?
        AND store.area_id IN ? 
        GROUP BY area_name';

        $chart = $db->query($sql, [$dateFrom, $dateTo, $area]);

        return $this->response->setStatusCode(200)->setJson(
            $chart->getResult(),
        );
  
    }

    public function table(){

        $area = $this->request->getVar('area');
        $dateFrom = $this->request->getVar('dateFrom');
        $dateTo = $this->request->getVar('dateTo');
        $db = \Config\Database::connect();
        
        $sql = 'SELECT 
        area_name,
        brand_name,
        SUM(compliance) / count(compliance) * 100 AS value
        FROM report_product 
        JOIN product ON report_product.product_id = product.product_id 
        JOIN store ON report_product.store_id = store.store_id 
        JOIN product_brand ON product_brand.brand_id = product.brand_id 
        JOIN store_area ON store_area.area_id = store.area_id 
        WHERE tanggal BETWEEN ? AND ?
        AND store.area_id IN ? 
        GROUP BY brand_name , area_name
        ORDER BY area_name';

        $test = $db->query($sql, [$dateFrom, $dateTo, $area]);

        return $this->response->setStatusCode(200)->setJson(
            $test->getResult()
        );

}

  
}
