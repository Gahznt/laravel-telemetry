<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Instrumentation\Configurator;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Util\ShutdownHandler;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProviderBuilder;

class OtelTracerProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
            Globals::registerInitializer(function (Configurator $configurator) {
            $transport = (new OtlpHttpTransportFactory())->create('http://jaeger:4318/v1/traces', 'application/x-protobuf');
            $exporter = new SpanExporter($transport);
            $spanProcessor = new SimpleSpanProcessor($exporter);

            $tracerProvider = (new TracerProviderBuilder())
                ->addSpanProcessor($spanProcessor)
                ->setSampler(new ParentBased(new AlwaysOnSampler()))
                ->build();

            ShutdownHandler::register([$tracerProvider, 'shutdown']);

            return $configurator
                ->withTracerProvider($tracerProvider);
        });
    }
}
