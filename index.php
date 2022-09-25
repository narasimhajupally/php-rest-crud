<?php
require __DIR__ . "/include/bootstrap.php";
require PROJECT_ROOT_PATH . "/Router.php";
require PROJECT_ROOT_PATH . "/Route.php";

require PROJECT_ROOT_PATH . "/Controller/Api/ModuleController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/AssessmentController.php";

// global $router;
$router = new Router([
    new Route('modules.list', '/~sgschene/v1/module/list', [ModuleController::class, 'list'], ['GET']),
    new Route('modules.create', '/~sgschene/v1/module/create', [ModuleController::class, 'create'], ['POST']),
    new Route('assessment.list', '/~sgschene/v1/module/{m_id}/assessment/list', [AssessmentController::class, 'list'], ['GET']),
    new Route('assessment.create', '/~sgschene/v1/module/{m_id}/assessment/create', [AssessmentController::class, 'create'], ['POST']),
    new Route('assessment.update', '/~sgschene/v1/module/{m_id}/assessment/{a_id}/update', [AssessmentController::class, 'update'], ['PUT']),
    new Route('assessment.delete', '/~sgschene/v1/assessment/{a_id}/delete', [AssessmentController::class, 'delete'], ['DELETE']),
]);

if ($_SERVER['REQUEST_URI'] == '/') {
    header("Location: /modules.html");
    exit();
}

try {
    // $_SERVER['REQUEST_URI'] = '/api/articles/2'
    // $_SERVER['REQUEST_METHOD'] = 'GET'
    $route = $router->matchFromPath($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

    $parameters = $route->getParameters();
    // $arguments = ['id' => 2]
    $arguments = $route->getVars();

    $controllerName = $parameters[0];
    $methodName = $parameters[1] ?? null;

    $controller = new $controllerName();
    if (!is_callable($controller)) {
        $controller =  [$controller, $methodName];
    }

    $controller(...array_values($arguments));
} catch (\Exception $exception) {
    header("HTTP/1.0 404 Not Found");
    echo json_encode(['status' => 'fail', 'message' => $exception->getMessage()]);
}
