; ---------------------------------------------------------------
; MyAPIs - PHP runtime configuration (template)
; ---------------------------------------------------------------
; Placeholders below are replaced by docker/entrypoint.sh using
; the values defined in `.env` (see example.env).
; ---------------------------------------------------------------

memory_limit = __PHP_MEMORY_LIMIT__
upload_max_filesize = __PHP_UPLOAD_MAX_FILESIZE__
post_max_size = __PHP_POST_MAX_SIZE__
max_execution_time = 60
date.timezone = __PHP_DATE_TIMEZONE__
expose_php = Off
display_errors = __APP_ENV_DISPLAY_ERRORS__
error_reporting = E_ALL
