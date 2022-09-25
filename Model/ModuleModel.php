<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class ModuleModel extends Database
{
    public function getModules()
    {
        global $router;
        $sql = "SELECT * FROM modules ORDER BY code ASC";
        $pdo = $this->connection->prepare($sql);
        $pdo->execute();
        $modules = $pdo->fetchAll(PDO::FETCH_ASSOC);
        foreach ($modules as $index => $row) {
            $modules[$index]['assessments_link'] = $router->generateUri('assessment.list', ['m_id'=>$row['id']]);
        }
        return $modules;
    }

    public function addModule($code, $title)
    {
        $sql = "INSERT INTO modules (code,title) VALUES (?,?)";
        $pdo = $this->connection->prepare($sql);
        $pdo->execute([$code, $title]);
    }

    public function moduleExists($moduleId)
    {
        $sql = "SELECT * FROM modules where id = ?";
        $pdo = $this->connection->prepare($sql);
        $pdo->bindParam(1, $moduleId, PDO::PARAM_INT);
        $pdo->execute();
        $module = $pdo->fetch(PDO::FETCH_ASSOC);
        return !empty($module);
    }

    public function getModuleByCode($code)
    {
        $sql = "SELECT * FROM modules where code = ?";
        $pdo = $this->connection->prepare($sql);
        $pdo->execute([$code]);
        $module = $pdo->fetch(PDO::FETCH_ASSOC);
        return $module;
    }
}
