<?php

namespace App\Service;

class NosqlStatsService
{
    private string $filePath;

    public function __construct(string $projectDir)
    {
    $dir = $projectDir . '/var';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $this->filePath = $dir . '/stats_nosql.json';
    }
    

    public function addStat(float $credits): void
    {
        dump("Le service NoSQL est bien appelé !");
        $data = $this->getStats();
        $today = (new \DateTime())->format('Y-m-d');

        if (!isset($data[$today])) {
            $data[$today] = ['reservations' => 0, 'credits' => 0];
        }

        $data[$today]['reservations']++;
        $data[$today]['credits'] += $credits;

        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getStats(): array
    {

    if (!file_exists($this->filePath)) {
        return [];
    }

    $content = file_get_contents($this->filePath);
    
    // Si le fichier est vide, on retourne un tableau vide au lieu de null
    if (empty($content)) {
        return [];
    }

    $data = json_decode($content, true);

    // On s'assure de toujours retourner un tableau (array)
    return is_array($data) ? $data : [];
    }


    //messages visiteur//
    public function addContactMessage(array $messageData): void
    {
        $data = $this->getStats(); // On récupère le contenu actuel du fichier JSON
    
    // init tableau message
    if (!isset($data['messages'])) {
        $data['messages'] = [];
    }

    // On ajoute le nouveau message avec une date et un ID unique
    $messageData['id'] = uniqid();
    $messageData['date'] = (new \DateTime())->format('Y-m-d H:i');
    $messageData['lu'] = false;

    $data['messages'][] = $messageData;

    // On enregistre tout dans le fichier var/stats_nosql.json
    file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
    
}
