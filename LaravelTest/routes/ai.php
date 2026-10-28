<?php

use App\Mcp\Servers\KI;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/ki',KI::class);

Mcp::local('/mcp/ki',KI::class);