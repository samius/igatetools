<?php
namespace Igate\Plugins;

/**
 * Automaticky prevadi camelCase atributy entit na under_score nazvy sloupcu v db a naopak.
 *
 * @category Igate
 * @package Doctrine
 * @author milan
 */
class DoctrineEventListener
{
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $args)
    {
        $metadata = $args->getClassMetadata();
        //zpracuju název tabulky
        $metadata->table['name'] = $this->sanitizeName($metadata->table['name']);
        
        //názvy mapování
        foreach ($metadata->fieldMappings as $key=>$value) {
            $metadata->fieldMappings[$key]['columnName'] = $this->sanitizeName($value['columnName']);
        }
        
        //a názvy sloupců
        foreach ($metadata->columnNames as $key=>$value) {
            $metadata->columnNames[$key] = $this->sanitizeName($value);
        }
        foreach ($metadata->fieldNames as $key=>$value) {
            unset($metadata->fieldNames[$key]);
            $metadata->fieldNames[$this->sanitizeName($key)] = $value; 
        }
    }
    
    /**
     * Před každou perzistací spustí validaci, je-li definovaná
     * @param $args
     */
    public function prePersist($args)
    {
        $entity = $args->getEntity();
        if (method_exists($entity, 'validate')) {
            $entity->validate();
        }
    }
    
    /**
     * Metoda změní názvy tabulek a sloupců z camelCase na underscore_case.
     * @param string $str
     * @return string
     */
    private function sanitizeName($str)
    {
        //pokud je první písmeno velké, změním ho na malé
        $str = lcfirst($str);
        //"tableID" rozloží na "table_i_d"
        return strtolower(preg_replace('/([A-Z])/', '_$1', $str));
        
        //"tableID" rozloží na "table_id"
        //return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $str));
    }
}
