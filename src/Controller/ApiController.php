<?php
namespace App\Controller;

// use \Psr\Http\Message\ServerRequestInterface as Request;
// use \Psr\Http\Message\ResponseInterface as Response;


public class ApiController {

    public function __construct() {

    }


// -----------------------------------------------------------------
// GET functions
// -----------------------------------------------------------------

    /**
     * @return JSON document with all items
     */
    public function getAll($request, $response) {
        // try {
        //     // query DB for all objects
        //     $result = $this->$db->getAll();
        //     if($result) {
        //         $logger->info("getAll request successful");
        //        // construct json with collection
        //         return $response->withStatus(200)
        //                         ->withHeader("Content-Type", "application/json")
        //                         ->write($result); 
        //     } else {
        //         $logger->info("getAll request unsuccessful, no records found.");
        //         throw new PDOException("No records found.");
        //     }
        // } catch(PDOException $e) {
        //     $logger->info("getAll request unsuccessful, 404 error");
        //     $response->withStatus(404)
        //              ->write('{"error":{"text":'. $e->getMessage() .'}}');
        // }
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json")
                        ->write("Brah"); 

    }

    /**
     * @param $item is a json with some fields
     * @return 
     */
    public function addItem($item) {

    }


// -----------------------------------------------------------------
// POST functions
// -----------------------------------------------------------------

    /**
     * 
     */
    public function addItem(Request $request, Response $response) {
        if (is_null($request->getParsedBody()))
        {
            $response->getBody()->write("Invalid JSON document.");
            $response->withStatus(400);
            return $response;
        }
        try {
            print_r($request->getParsedBody());
            $result = db_addEquipment($request->getParsedBody());
            print_r($result);
        } catch(PDOException $e) {
            $app->response()->setStatus(404);
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
}