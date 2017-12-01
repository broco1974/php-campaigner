<?php

namespace Broco\Campaigner\Soap;

/**
 * Class WorkflowManager
 *
 * @package Broco\Campaigner\Soap
 */
class WorkflowManager extends AbstractSoapClient
{
    /**
     * @var string
     */
    const DEFAULT_WSDL = 'https://ws.campaigner.com/2013/01/workflowmanagement.asmx?WSDL';

    /**
     * This web method returns information about each workflow, including the name, identifier, and status.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 177
     *
     * @param bool $onlyApiTriggered
     * @param bool $onlyActiveAndTest
     *
     * @return array|bool|null
     */
    public function ListWorkflows($onlyApiTriggered, $onlyActiveAndTest)
    {
        return $this->callMethod(__FUNCTION__, [
            'onlyApiTriggered' => $onlyApiTriggered,
            'onlyActiveAndTest' => $onlyActiveAndTest,
        ]);
    }

    /**
     * This web method triggers a specified workflow for selected contacts.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 180
     *
     * @param int    $workflowId
     * @param string $xmlContactQuery
     *
     * @return array|bool|null
     */
    public function TriggerWorkflow($workflowId, $xmlContactQuery)
    {
        if (false === $this->isValidXmlContactQuery($xmlContactQuery)) {
            return false;
        }

        return $this->callMethod(__FUNCTION__, [
            'workflowId' => $workflowId,
            'xmlContactQuery' => $xmlContactQuery,
        ]);
    }
}
