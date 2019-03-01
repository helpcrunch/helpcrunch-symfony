<?php

namespace Helpcrunch\Response;

class InnerErrorCodes
{
    const POST_ENTITY_VALIDATION_FAILED = 'create_entity_fields_validation_failed';
    const PUT_ENTITY_VALIDATION_FAILED = 'update_entity_fields_validation_failed';
    const ENTITY_DOES_NOT_EXIST = 'entity_does_not_exist';
    const INVALID_PARAMETER = 'invalid_parameter';
    const INVALID_ENTITY_ID = 'invalid_entity_id';
}
