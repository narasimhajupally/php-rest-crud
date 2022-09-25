<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class AssessmentModel extends Database
{
    public function getAssessments($moduleId)
    {
        $sql = "SELECT * FROM assessments where module_id=?";
        $pdo = $this->connection->prepare($sql);
        $pdo->execute([$moduleId]);
        $assessments = $pdo->fetchAll(PDO::FETCH_ASSOC);
        return $assessments;
    }

    public function addAssessment($moduleId, $type, $description, $weight)
    {
        $sql = "INSERT INTO assessments (module_id,type,description,weight) VALUES (?,?,?,?)";
        $pdo = $this->connection->prepare($sql);

        $pdo->execute([$moduleId, $type, $description, $weight]);
    }

    public function updateAssessment($assessmentId, $type, $description, $weight)
    {
        $sql = "UPDATE assessments SET type=?,description=?,weight=? WHERE id=?";
        $pdo = $this->connection->prepare($sql);

        $pdo->execute([$type, $description, $weight, $assessmentId]);
    }

    public function getAssessment($assessmentId, $moduleId = null)
    {
        if ($moduleId != null) {
            $sql = "SELECT * FROM assessments where module_id = ? and id=?";
            $pdo = $this->connection->prepare($sql);
            $pdo->execute([$moduleId, $assessmentId]);
        } else {
            $sql = "SELECT * FROM assessments where id=?";
            $pdo = $this->connection->prepare($sql);
            $pdo->execute([$assessmentId]);
        }

        $assessment = $pdo->fetch(PDO::FETCH_ASSOC);
        return $assessment;
    }

    public function delete($assessmentId)
    {
        $sql = "DELETE FROM assessments WHERE id=?";
        $pdo = $this->connection->prepare($sql);
        $pdo->execute([$assessmentId]);
    }

    public function getTotalWeight($moduleId)
    {
        $r = 0;
        $assessments = $this->getAssessments($moduleId);
        foreach ($assessments as $assessment) {
            $r = $r + $assessment['weight'];
        }
        return $r;
    }
}
