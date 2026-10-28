<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('K I')]
#[Version('0.0.1')]
#[Instructions('
    This server is designed to provide a comprehensive and interactive experience for users seeking information, assistance, and engagement. It leverages advanced AI capabilities to deliver accurate and relevant responses across a wide range of topics. Users can expect a seamless and intuitive interface that facilitates easy access to information, resources, and tools. The server is optimized for performance, ensuring quick response times and reliable service. Whether you are looking for answers to specific questions, exploring new ideas, or seeking guidance on various subjects, this server is equipped to meet your needs effectively.')]
class KI extends Server
{
    protected array $tools = [
        //
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
