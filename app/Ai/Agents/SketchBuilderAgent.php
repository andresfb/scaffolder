<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Tools\RandomPromptTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Config;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;

final class SketchBuilderAgent implements Agent, HasStructuredOutput, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): string
    {
        return Config::string('sketches.agent_instruction');
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            resolve(RandomPromptTool::class),
        ];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'hash' => $schema->string()->required(),
            'title' => $schema->string()->required(),
            'outline' => $schema->string()->required(),
            'draft' => $schema->string()->required(),
        ];
    }
}
