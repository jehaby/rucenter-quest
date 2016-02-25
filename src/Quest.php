<?php

namespace Jehaby\Quest;


use SpaceWeb\Quest\QuestAbstract;


/**
 * Class Quest
 * @package Jehaby\Quest
 */
class Quest extends QuestAbstract {

    const WITHOUT_DOCUMENTS = 0;

    const WITH_DOCUMENTS = 1;


    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getStatisticsWithDocuments($startDate, $endDate)
    {
        return $this->getStatistics($startDate, $endDate, self::WITH_DOCUMENTS);
    }


    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getStatisticsWithoutDocuments($startDate, $endDate)
    {
        return $this->getStatistics($startDate, $endDate, self::WITHOUT_DOCUMENTS);
    }


    /**
     * @param $startDate
     * @param $endDate
     * @param $type
     * @return array
     */
    private function getStatistics($startDate, $endDate, $type)
    {
        $sql = '
SELECT count(*) `count`, sum(amount) `amount` FROM payments
WHERE id ' .
            ($type === self::WITHOUT_DOCUMENTS ? ' NOT ' : '') .
            'IN (SELECT entity_id from documents)
      AND create_ts > ?
      AND create_ts < ?
';
        $statement = $this->getDb()->prepare($sql);
        $statement->execute([$startDate, $endDate]);
        return $statement->fetch(\PDO::FETCH_NUM) ?: [];
    }

}
