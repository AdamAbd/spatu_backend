<?php

namespace App\Http\Helper;

class ResponseCode
{
    const success = 200;
    const created = 201;
    const deleted = 200;
    const updated = 200;
    const no_content = 204;
    const invalid_request = 400;
    const unsupported_response_type = 400;
    const invalid_scope = 400;
    const invalid_grant = 400;
    const invalid_credentials = 400;
    const invalid_refresh = 400;
    const no_data = 400;
    const invalid_data = 400;
    const access_denied = 401;
    const unauthorized = 401;
    const invalid_client = 401;
    const forbidden = 403;
    const resource_not_found = 404;
    const not_acceptable = 406;
    const resource_exists = 409;
    const conflict = 409;
    const resource_gone = 410;
    const payload_too_large = 413;
    const unsupported_media_type = 415;
    const too_many_requests = 429;
    const server_error = 500;
    const unsupported_grant_type = 501;
    const not_implemented = 501;
    const temporarily_unavailable = 503;
}
