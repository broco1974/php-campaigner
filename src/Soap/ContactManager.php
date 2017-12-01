<?php

namespace Broco\Campaigner\Soap;

use Broco\Campaigner\Soap\Model\CustomAttribute;
use Broco\Campaigner\Soap\Model\NullableString;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class SoapClient
 *
 * @package Broco\Campaigner\Soap
 */
class ContactManager extends AbstractSoapClient
{

    /**
     * @var string
     */
    const DEFAULT_WSDL = 'https://ws.campaigner.com/2013/01/contactmanagement.asmx?WSDL';

    /**
     * This web method adds a custom contact attribute, or updates a default or custom contact attribute for all contacts.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 27
     *
     * @param int    $attributeId
     * @param string $attributeName
     * @param string $attributeType
     * @param string $defaultValue
     * @param bool   $clearDefault
     *
     * @return mixed|null
     */
    public function CreateUpdateAttribute(
        $attributeId,
        $attributeName,
        $attributeType,
        $defaultValue = '',
        $clearDefault = true
    ) {
        $parameters = [
            'attributeId' => $attributeId,
            'attributeName' => $attributeName,
            'attributeType' => $attributeType,
            'clearDefault' => $clearDefault,
        ];

        if (false === $clearDefault) {
            $parameters['defaultValue'] = $defaultValue;
        }

        return $this->callMethod(__FUNCTION__, $parameters);
    }

    /**
     * This web method deletes an existing custom attribute.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 30
     *
     * @param int $id
     *
     * @return mixed|null
     */
    public function DeleteAttribute($id)
    {
        return $this->callMethod(__FUNCTION__, [
            'id' => $id,
        ]);
    }

    /**
     * This web method deletes one or more specified contacts.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 32
     *
     * @param array $contactKeys
     *
     * @return mixed|null
     */
    public function DeleteContacts(array $contactKeys)
    {
        return $this->callMethod(__FUNCTION__, [
            'contactKeys' => $this->validateContactKeys($contactKeys),
        ]);
    }

    /**
     * This web method returns various untyped reports based on the contacts obtained using the
     * RunReport web method, as described in "RunReport Web Method".
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 34
     *
     * @param string      $reportTicketId
     * @param int         $fromRow
     * @param int         $toRow
     * @param string|null $reportType
     *
     * @return mixed|null
     */
    public function DownloadReport($reportTicketId, $fromRow = 1, $toRow = 100, $reportType = null)
    {
        $parameters = [
            'reportTicketId' => $reportTicketId,
            'fromRow' => $fromRow,
            'toRow' => $toRow,
        ];

        if (null !== $reportType) {
            $parameters['reportType'] = $this->validateReportType($reportType);
        }

        return $this->callMethod(__FUNCTION__, $parameters);
    }

    /**
     * This web method returns information about attributes for up to 1000 specified contacts.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 37
     *
     * @param array $contactKeys
     * @param bool  $includeStaticAttributes
     * @param bool  $includeCustomAttributes
     * @param bool  $includeSystemAttributes
     * @param bool  $includeGroupMembershipsAttributes
     *
     * @return mixed|null
     */
    public function GetContacts(
        array $contactKeys,
        $includeStaticAttributes = false,
        $includeCustomAttributes = false,
        $includeSystemAttributes = false,
        $includeGroupMembershipsAttributes = false
    ) {
        return $this->callMethod(__FUNCTION__, [
            'contactFilter' => [
                'ContactKeys' => $this->validateContactKeys($contactKeys),
            ],
            'contactInformationFilter' => [
                'IncludeStaticAttributes' => $includeStaticAttributes,
                'IncludeCustomAttributes' => $includeCustomAttributes,
                'IncludeSystemAttributes' => $includeSystemAttributes,
                'IncludeGroupMembershipsAttributes' => $includeGroupMembershipsAttributes,
            ],
        ]);
    }

    /**
     * This web method returns the results from uploaded contacts for a request initiated by UploadMassContacts.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 45
     *
     * @param int $uploadTicketId
     *
     * @return mixed|null
     */
    public function GetUploadMassContactsResult($uploadTicketId)
    {
        return $this->callMethod(__FUNCTION__, [
            'uploadTicketId' => $uploadTicketId,
        ]);
    }

    /**
     * This web method returns the current status for an upload request initiated by the UploadMassContacts web method.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 49
     *
     * @param $uploadTicketId
     *
     * @return array|bool|null
     */
    public function GetUploadMassContactsStatus($uploadTicketId)
    {
        return $this->callMethod(__FUNCTION__, [
            'uploadTicketId' => $uploadTicketId,
        ]);
    }

    /**
     * This web method synchronously adds contacts and defines their information, or updates information for existing contacts for up to 1000 contacts.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 53
     *
     * @param bool       $updateExistingContacts
     * @param bool       $triggerWorkflow
     * @param array      $contacts
     * @param array|null $globalAddToGroup
     * @param array|null $globalRemoveFromGroup
     *
     * @return mixed|null
     */
    public function ImmediateUpload(
        $updateExistingContacts,
        $triggerWorkflow,
        array $contacts,
        array $globalAddToGroup = null,
        array $globalRemoveFromGroup = null
    ) {
        $parameters = [
            'UpdateExistingContacts' => $updateExistingContacts,
            'TriggerWorkflow' => $triggerWorkflow,
            'contacts' => $this->validateContactsData($contacts),
        ];

        if (null !== $globalAddToGroup) {
            $parameters['globalAddToGroup'] = $globalAddToGroup;
        }

        if (null !== $globalRemoveFromGroup) {
            $parameters['globalRemoveFromGroup'] = $globalRemoveFromGroup;
        }

        return $this->callMethod(__FUNCTION__, $parameters);
    }

    /**
     * The InitiateDoubleOptIn web method is used when requests to receive email campaigns are gathered by a custom application or interface, instead of a Campaigner® sign up form.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 63
     *
     * @param array  $contactKeys
     * @param string $xmlContactQuery
     * @param int    $formId
     *
     * @return mixed|null
     */
    public function InitiateDoubleOptIn(array $contactKeys, $xmlContactQuery, $formId)
    {
        if (false === $this->isValidXmlContactQuery($xmlContactQuery)) {
            return false;
        }

        return $this->callMethod(__FUNCTION__, [
            'contactFilter' => [
                'ContactKeys' => $this->validateContactKeys($contactKeys),
                'xmlContactQuery' => $xmlContactQuery,
            ],
            'formId' => $formId,
        ]);
    }

    /**
     * This web method lists all contact attributes and their properties (such as the identifier and type).
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 67
     *
     * @param bool $includeAllDefaultAttributes
     * @param bool $includeAllCustomAttributes
     * @param bool $includeAllSystemAttributes
     *
     * @return mixed|null
     */
    public function ListAttributes(
        $includeAllDefaultAttributes = true,
        $includeAllCustomAttributes = true,
        $includeAllSystemAttributes = true
    ) {
        return $this->callMethod(__FUNCTION__, [
            'filter' => [
                'IncludeAllDefaultAttributes' => $includeAllDefaultAttributes,
                'IncludeAllCustomAttributes' => $includeAllCustomAttributes,
                'IncludeAllSystemAttributes' => $includeAllSystemAttributes,
            ],
        ]);
    }

    /**
     * This web method lists all contact fields and their properties (such as the identifier and type).
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 71
     *
     * @param bool $includeAllDefaultAttributes
     * @param bool $includeAllCustomAttributes
     * @param bool $includeAllSystemAttributes
     *
     * @return mixed|null
     */
    public function ListContactFields(
        $includeAllDefaultAttributes = true,
        $includeAllCustomAttributes = true,
        $includeAllSystemAttributes = true
    ) {
        return $this->callMethod(__FUNCTION__, [
            'filter' => [
                'IncludeAllDefaultAttributes' => $includeAllDefaultAttributes,
                'IncludeAllCustomAttributes' => $includeAllCustomAttributes,
                'IncludeAllSystemAttributes' => $includeAllSystemAttributes,
            ],
        ]);
    }

    /**
     * This web method lists the top 5000 test contacts associated with an account. You can specify a value to limit results.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 75
     *
     * @param int $contactCount
     *
     * @return mixed|null
     */
    public function ListTestContacts($contactCount)
    {
        return $this->callMethod(__FUNCTION__, [
            'contactCount' => $contactCount,
        ]);
    }

    /**
     * This web method changes one contact's status from Unsubscribed to Subscribed, HardBounce, SoftBounce, or Pending.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 78
     *
     * @param int    $contactId
     * @param string $contactUniqueIdentifier
     * @param string $status
     *
     * @return mixed|null
     */
    public function ResubscribeContact($contactId, $contactUniqueIdentifier, $status)
    {
        return $this->callMethod(__FUNCTION__, [
            'contactKey' => $this->validateContactKeys([
                'ContactId' => $contactId,
                'ContactUniqueIdentifier' => $contactUniqueIdentifier,
            ]),
            'status' => $this->validateStatus($status),
        ]);
    }

    /**
     * Undocumented method of the API but that actually exists.
     * Seems very similar to "UploadMassContacts" since all parameters are the same.
     *
     * @param bool       $updateExistingContacts
     * @param bool       $triggerWorkflow
     * @param array      $contacts
     * @param array|null $globalAddToGroup
     * @param array|null $globalRemoveFromGroup
     *
     * @return mixed
     */
    public function ReUploadContactsNV(
        $updateExistingContacts,
        $triggerWorkflow,
        array $contacts,
        array $globalAddToGroup = null,
        array $globalRemoveFromGroup = null
    ) {
        $parameters = [
            'UpdateExistingContacts' => $updateExistingContacts,
            'TriggerWorkflow' => $triggerWorkflow,
            'contacts' => $this->validateContactData($contacts),
        ];

        if (null !== $globalAddToGroup) {
            $parameters['globalAddToGroup'] = $globalAddToGroup;
        }

        if (null !== $globalRemoveFromGroup) {
            $parameters['globalRemoveFromGroup'] = $globalRemoveFromGroup;
        }

        return $this->callMethod(__FUNCTION__, $parameters);
    }

    /**
     * This web method processes an XML query string to obtain rows of contact information, which are then stored on Campaigner®.
     * The web method also returns a ticket ID for the query request and the number of rows obtained.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 82
     *
     * @param string $xmlContactQuery
     *
     * @return mixed|null
     */
    public function RunReport($xmlContactQuery)
    {
        if (false === $this->isValidXmlContactQuery($xmlContactQuery)) {
            return false;
        }

        return $this->callMethod(__FUNCTION__, [
            'xmlContactQuery' => $xmlContactQuery,
        ]);
    }

    /**
     * Like ImmediateUpload, the UploadMassContacts web method uploads contact information for multiple contacts at the same time to Campaigner®,
     * and performs additional processing, such as changing group memberships for contacts being uploaded.
     *
     * @see docs/Campaigner-Elements-API-User-Guide.pdf page 85
     *
     * @param bool       $updateExistingContacts
     * @param bool       $triggerWorkflow
     * @param array      $contacts
     * @param array|null $globalAddToGroup
     * @param array|null $globalRemoveFromGroup
     *
     * @return mixed
     */
    public function UploadMassContacts(
        $updateExistingContacts,
        $triggerWorkflow,
        array $contacts,
        array $globalAddToGroup = null,
        array $globalRemoveFromGroup = null
    ) {
        $parameters = [
            'UpdateExistingContacts' => $updateExistingContacts,
            'TriggerWorkflow' => $triggerWorkflow,
            'contacts' => $this->validateContactData($contacts),
        ];

        if (null !== $globalAddToGroup) {
            $parameters['globalAddToGroup'] = $globalAddToGroup;
        }

        if (null !== $globalRemoveFromGroup) {
            $parameters['globalRemoveFromGroup'] = $globalRemoveFromGroup;
        }

        return $this->callMethod(__FUNCTION__, $parameters);
    }

    /**
     * Formats "ContactUniqueIdentifier" request node.
     *
     * @param array $contactKeys
     *
     * @return array
     */
    private function validateContactKeys(array $contactKeys)
    {
        $contactKeys = array_map(function ($contactKey) {
            return $this->validateContactKey($contactKey);
        }, $contactKeys);

        return $contactKeys;
    }

    /**
     * Validates "ContactUniqueIdentifier" request node.
     *
     * @param array $contactKey
     *
     * @return array
     */
    private function validateContactKey(array $contactKey)
    {
        if (false === isset($contactKey['ContactUniqueIdentifier'])) {
            throw new InvalidConfigurationException('Missing "ContactUniqueIdentifier" parameter.');
        }

        if (isset($contactKey['ContactId'])) {
            return [
                'ContactId' => (int)$contactKey['ContactId'],
                'ContactUniqueIdentifier' => (string)$contactKey['ContactUniqueIdentifier'],
            ];
        }

        return [
            'ContactId' => 0,
            'ContactUniqueIdentifier' => (string)$contactKey['ContactUniqueIdentifier'],
        ];
    }

    /**
     * Validates "contactData" request node.
     *
     * @param array $contactData
     *
     * @return array
     */
    private function validateContactsData(array $contactsData)
    {
      $validatedContactsData = [];

      foreach ($contactsData as $contactData) {
        $validatedContactsData[] = $this->validateContactData($contactData);
      }

      return $validatedContactsData;
    }

    /**
     * Validates "contactData" request node.
     *
     * @param array $contactData
     *
     * @return array
     */
    private function validateContactData(array $contactData)
    {
        $validatedContactData = [];

        if (false === isset($contactData['ContactKey'])) {
            throw new InvalidConfigurationException('Missing "ContactKey" parameter.');
        }

        $validatedContactData['ContactKey'] = $this->validateContactKey($contactData['ContactKey']);

        foreach (['EmailAddress', 'FirstName', 'LastName', 'PhoneNumber', 'Fax'] as $key) {
          if (array_key_exists($key, $contactData)) {
            $validatedContactData[$key] = new NullableString(
                !isset($contactData[$key]),
                (string)$contactData[$key]
            );
          }
        }

        if (array_key_exists('Status', $contactData)) {
          $validatedContactData['Status'] = isset($contactData['Status']) ? $this->validateStatus($contactData['Status']) : null;
        }

        if (array_key_exists('Status', $contactData)) {
          $validatedContactData['MailFormat'] = isset($contactData['MailFormat']) ? $this->validateFormat($contactData['MailFormat']) : null;
        }

        if (array_key_exists('Status', $contactData)) {
          $validatedContactData['IsTestContact'] = isset($contactData['IsTestContact']) ? (bool)$contactData['IsTestContact'] : null;
        }

        if (true === isset($contactData['CustomAttributes'])) {
            $customAttributes = [];

            foreach ($contactData['CustomAttributes'] as $customAttributeId => $customAttributeValue) {
                $customAttributes[] = new CustomAttribute(
                    $customAttributeId,
                    empty($customAttributeValue),
                    empty($customAttributeValue) ? null : $customAttributeValue
                );
            }

            $validatedContactData['CustomAttributes'] = $customAttributes;
        }

        if (true === isset($contactData['AddToGroup'])) {
            $validatedContactData['AddToGroup'] = $contactData['AddToGroup'];
        }

        if (true === isset($contactData['RemoveFromGroup'])) {
            $validatedContactData['RemoveFromGroup'] = $contactData['RemoveFromGroup'];
        }

        return $validatedContactData;
    }

    /**
     * Validates "Status" request value.
     *
     * @param string $status
     *
     * @return string
     */
    private function validateStatus($status)
    {
        $statuses = [
            'Unsubscribed',
            'Subscribed',
            'HardBounce',
            'SoftBounce',
            'Pending',
        ];

        if (false === in_array($status, $statuses)) {
            throw new InvalidConfigurationException(sprintf('Invalid Status "%s".', $status));
        }

        return $status;
    }
}
