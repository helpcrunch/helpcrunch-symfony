<?php

namespace Helpcrunch\Response;

class InnerErrorCodes
{
    const UNAUTHORIZED = 'unauthorized';
    const ENTITY_NOT_FOUND = 'entity_not_found';
    const ENTITY_ALREADY_EXIST = 'entity_already_exist';
    const INVALID_ENTITY_ID = 'invalid_entity_id';
    const INVALID_PARAMETER = 'invalid_parameter';
    const MALFORMED_JSON = 'malformed_json';
    const MISSING_PARAMETER = 'missing_parameter';
    const PARENT_ENTITIES_MISMATCH = 'parent_entities_mismatch';
    const POST_ENTITY_VALIDATION_FAILED = 'create_entity_fields_validation_failed';
    const PUT_ENTITY_VALIDATION_FAILED = 'update_entity_fields_validation_failed';
    const SERVER_ERROR = 'server_error';
}
