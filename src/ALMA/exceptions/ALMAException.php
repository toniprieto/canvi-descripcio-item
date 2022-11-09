<?php
namespace UPCSBPA\CanviDescripcioItem\ALMA\exceptions;

class ALMAException extends \Exception
{

    //LListat de codis d'errors enviats per errors HTTP 400 de l'API d'ALMA
    /**
     Obtenir usuari (GET)
    401890 - 'User with identifier X of type Y was not found.'
    401861 - 'User with identifier X was not found.'
    4019990 - 'User with source identifier X of institution Y was not found.'
    4019998 - 'User with linking ID not found.'
    60101 - 'General Error: An error has occurred while processing the request.'
     */
    const GET_USER_TYPE_NOTFOUND = 401890;
    const GET_USER_NOTFOUND = 401861;
    const USER_SOURCEIDENTIFIER_INSTITUTION_NOTFOUND = 4019990;
    const USER_LINKING_ID_NOTFOUND = 4019998;
    const GENERAL_ERROR_USER = 60101;

    /**
     Autenticar usuaris (POST)
    401866 - 'User authentication failed'
    401890 - 'User was not found.'
    401861 - 'Refresh user with given identifier not found.'
    60229 - 'Failed to find linked account for user.'
    60226 - 'Fulfillment network copied user not found.'
    60230 - 'Failed to refresh linked user.'
     */
    const AUTHENTICATION_FAILED = 401866;
    const AUTHENTICATION_USER_NOTFOUND = 401890;
    const REFRESH_USER_NOTFOUND = 401861;
    const FAILED_TO_FIND_LINKED_ACCOUNT = 60229;
    const FULLFILLEMENT_NETWORD_USER_NOTFOUND = 60226;

    /**
    Crear usuari (PUT)
    401890 - 'User with identifier X of type Y was not found.'
    401859 - 'Action currently not supported.'
    401676 - 'No valid XML was given.'
    401858 - 'The external id in DB does not fit the given value in xml - external id cannot be updated.'
    401855 - 'The account type 'Internal with external authentication' is currently not supported.'
    500038 - 'New password must be at least 8 characters long and must not include the user-name or any commonly used password.'
    401860 - 'Failed to update user.'
    401652 - 'General Error: An error has occurred while processing the request.'
    401863 - 'Given X type (Y) is not supported for given user record type (Z).'
    401864 - 'Given X type (Y) is invalid.'
     */
    const ACTION_NO_SUPPORTED = 401859;
    const INVALID_XML = 401676;
    const INVALID_EXTERNAL_ID = 401858;
    const ACCOUNT_TYPE_NO_SUPPORTED = 401855;
    const NEW_PASSSWORD_INSECURE = 500038;
    const FAILED_UPDATE_USER = 401860;
    const GENERAL_ERROR = 401652;
    const NOTSUPPORTED_TTPE_FIELD1 = 401863;
    const NOTSUPPORTED_TYPE_FIELD2 = 401864;


    /**
     * Set members (GET ID)
    60107 - 'Invalid set ID.'
     */
    const INVALID_SET_ID = 60107;

    /**
     jobs (get list)
     40166410 - 'Invalid category.'
     40166410 - 'Invalid type.'
     402219 - 'Failed to retrieve jobs list.'
     */
    const INVALID_CATEGORY_JOB = 40166410;
    const INVALID_TYPE_JOB = 40166410;
    const FAILED_RETRIEVE_JOBS = 402219;

    /**
     job (get one)
    402215 - 'Invalid job id format.'
    402216 - 'Invalid job id.'
    402249 - 'Invalid scheduled job category.'
     */
    const INVALID_JOB_ID_FORMAT = 402215;
    const INVALID_JOB_ID = 402216;
    const INVALID_SCHEDULED_JOB_CATEGORY = 402249;


    /*
    Run job (POST)
    402215 - 'Invalid job id format.'
    402216 - 'Invalid job id.'
    402220 - 'Operation was not provided.'
    402221 - 'Operation is not supported.'
    402222/402223 - 'Execution threshold reached.'
    402224/402225/402226 - 'An internal error occured.'
    402228 - 'Mandatory parameter is missing from input.'
    402229 - 'Mandatory parameter value is empty.'
    402248 - 'Cannot submit scheduled job.'
    402249 - 'Invalid scheduled job category.'
    402231 - 'Job in consisted of more than one task - executing such job is currently not supported via the API.'
     */
    const OP_NO_PROVIDED = 402220;
    const OP_NO_SUPPORTED = 402221;
    const THRESHOLD_REACHED1 = 402222;
    const THRESHOLD_REACHED2 = 402223;
    const INTERNAL_ERROR1 = 402224;
    const INTERNAL_ERROR2 = 402225;
    const INTERNAL_ERROR3 = 402226;
    const MANDATORY_PARAMETER_MISSING = 402228;
    const MANDATORY_PARAMETER_EMPTY = 402229;
    const CANNOT_SUBMIT = 402248;
    const NOT_AVAILABLE_API = 402231;

    /**
     Get bibliografic
    401652 - 'General Error - An error has occurred while processing the request.'
    402204 - 'Input parameters mmsId X is not numeric.'
    402203 - 'Input parameters mmsId X is not valid.'
     */
    const ID_NOT_NUMERIC = 402204;
    const ID_NOT_VALID = 402203;

    /**
     Get llistat items
    401652 - 'General Error - An error has occurred while processing the request.'
    402203 - 'The Bib Record is not valid.'
    401688 - 'Error while retrieving items.'
    402469 - 'Current library is not valid.'
    60215 - 'Current location is not valid.'
    60216 - 'The ordering is not valid.'
    60106 - 'Invalid query format.'
    60217 - 'The offset is too large.'
    402217 - 'Invalid date format for item filter.'
     */
    const INVALID_BIBRECORD = 402203;
    const ERROR_RETRIEVING_ITEMS = 401688;
    const INVALID_CURRENT_LIBRARY = 402469;
    const INVALID_CURRENT_LOCATION = 60215;
    const INVALID_ORDERING = 60216;
    const INVALID_QUERY = 60106;
    const OFFSET_TOO_LARGE = 60217;
    const INVALID_DATE_FORMAT = 402217;

    /**
     * @var null|mixed
     */
    private $contents;

    /**
     * @param $message string missatge de l'excepció
     * @param $code int codi de l'excepció
     * @param $contents string content contingut de l'excepció
     * @param \Exception $previous excepció previa
     */
    public function __construct($message = '', $code = 0, $previous = null, $contents = null)
    {
        parent::__construct($message, $code, $previous);
        $this->contents = $contents;
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param mixed $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    public function getPrintMessage($includecontent = false, $includetrace = false) {
        $result = $this->message . " (" . $this->code . ")\n";
        if ($includecontent) {
            if ($this->contents != null) {
                $result .= json_encode($this->contents, JSON_PRETTY_PRINT) . "\n";
            }
        }
        if ($includetrace) {
            if ($this->contents != null) {
                $result .= $this->getTraceAsString() . "\n";
            }
        }

        return $result;
    }

}