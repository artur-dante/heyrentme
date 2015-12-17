<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseAdminController {
     /**
     * 
     * @Route("/admin/users", name="admin_users_list")
     */
    public function indexAction() {
        return $this->render('admin/user/index.html.twig');
    }
    
    /**
     * @Route("/admin/users/jsondata", name="admin_users_jsondata")
     */
    public function JsonData(Request $request)
    {  
        $sortColumn = $request->get('sidx');
        $sortDirection = $request->get('sord');
        $pageSize = $request->get('rows');
        $page = $request->get('page');
        
        $fSubcategory = $request->get('s_name');
        $fName = $request->get('fs_name');
        $callback = $request->get('callback');
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:FeatureSection');
        $dataRows = $repo->getGridOverview($sortColumn, $sortDirection, $pageSize, $page, $fSubcategory, $fName);
        $rowsCount = $repo->countAll();
        $pagesCount = ceil($rowsCount / $pageSize);
        
        $rows = array(); // rows as json result
        
        foreach ($dataRows as $dataRow) { // build single row
            $row = array();
            $row['id'] = $dataRow->getId();
            $cell = array();
            $i = 0;
            $cell[$i++] = '';
            $cell[$i++] = $dataRow->getSubcategory()->getName();
            $cell[$i++] = $dataRow->getName();
            $cell[$i++] = $dataRow->getExclusive();
            $cell[$i++] = $dataRow->getPosition();
            $row['cell'] = $cell;
            array_push($rows, $row);
        }
        
        $result = array( // main result object as json
            'records' => $rowsCount,
            'page' => $page,
            'total' => $pagesCount,
            'rows' => $rows
        );        
        
        $resp = new JsonResponse($result, JsonResponse::HTTP_OK);
        $resp->setCallback($callback);
        return $resp;
    }
}
