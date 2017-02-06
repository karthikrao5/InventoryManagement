<?php
namespace App\Controller;

use Psr\Log\LoggerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


public class ApiController
{
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    // this is a default call route. display a json of 
    // active API routes
    public function __invoke(Request $request, Response $response, $args) {
        $this->logger->info("Home page action dispatched");
        $response->write("You have hit the API controller endpoint");
        return $response;
    }

// -----------------------------------------------------------------
// GET functions
// -----------------------------------------------------------------

    /**
     * @return JSON document with all items
     */
    public function getAll() {

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