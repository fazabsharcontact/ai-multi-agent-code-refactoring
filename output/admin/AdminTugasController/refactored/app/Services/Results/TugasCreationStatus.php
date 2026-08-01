<?php

namespace App\Services\Results;

enum TugasCreationStatus: string
{
    case Success = 'success';
    case MissingPemberi = 'missing_pemberi';
    case DatabaseError = 'database_error';
    case UnexpectedError = 'unexpected_error';
}