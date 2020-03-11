<?php

namespace Helpcrunch\Response;

class InnerErrorCodes
{
    const UNAUTHORIZED = 'unauthorized';
    const ENTITY_NOT_FOUND = 'entityNotFound';
    const ENTITY_ALREADY_EXIST = 'entityAlreadyExist';
    const INVALID_ENTITY_ID = 'invalidEntityId';
    const INVALID_PARAMETER = 'invalidParameter';
    const MALFORMED_JSON = 'malformedJson';
    const MISSING_PARAMETER = 'missingParameter';
    const PARENT_ENTITIES_MISMATCH = 'parentEntitiesMismatch';
    const POST_ENTITY_VALIDATION_FAILED = 'createEntityFieldsValidationFailed';
    const PUT_ENTITY_VALIDATION_FAILED = 'updateEntityFieldsValidationFailed';
    const SERVER_ERROR = 'serverError';
}
