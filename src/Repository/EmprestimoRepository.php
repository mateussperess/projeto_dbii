<?php

namespace Repository;

use Database\Database;
use Model\Emprestimo;
use PDO;

class EmprestimoRepository
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public function insert(Emprestimo $emprestimo): Emprestimo
    {
        $stmt = $this->connection->prepare("INSERT INTO emprestimos (idUser, idLivro, data_inicio, data_entrega)
                VALUES (:idUser, :idLivro, :inicio, :entrega)");
        $stmt->bindValue(":idUser", $emprestimo->getIdUser());
        $stmt->bindValue(":idLivro", $emprestimo->getIdLivro());
        $stmt->bindValue(":inicio", $emprestimo->getDataInicio());
        $stmt->bindValue(":entrega", $emprestimo->getDataEntrega());

        $stmt->execute();

        $emprestimo->setId($this->connection->lastInsertId());
        return $emprestimo;
    }

    public function updateEntrega($emprestimoId)
    {
        $stmt = $this->connection->prepare("UPDATE emprestimos SET data_entrega = :data_entrega WHERE id = :id");
        $stmt->bindValue(":id", $emprestimoId);
        $stmt->bindValue(":data_entrega",date("Y-m-d H:i:s"));

        $stmt->execute();

    }

    public function findOpenLoansByUser(int $idUser): array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM emprestimos WHERE idUser = :idUser  AND data_entrega IS NULL;"
        );
        $stmt->bindValue(":idUser", $idUser);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function findAllLoansByUser(int $idUser): array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM emprestimos WHERE idUser = :idUser;"
        );
        $stmt->bindValue(":idUser", $idUser);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function findOpenLoanByBook(int $idLivro): ?Emprestimo
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM emprestimos WHERE idLivro = :idLivro AND data_entrega IS NULL"
        );
        $stmt->bindValue(":idLivro", $idLivro);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Emprestimo(
            $data["id"],
            $data["idUser"],
            $data["idLivro"],
            $data["data_inicio"],
            $data["data_entrega"]
        ) : null;
    }

    public function findAllOpenLoans(): array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM emprestimos;"
        );
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
}
