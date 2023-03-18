<?php

namespace ReactorX\DependencyInjection;

/**
 * Enumeration of component scopes
 */
enum Scope: string
{
    /**
     * Instantiated once through the application lifetime
     */
    case Singleton = "SINGLETON";
    /**
     * Instantiated for every incoming http request
     */
    case Request = "REQUEST";
    /**
     * Instantiated every time it is requested
     */
    case Transient = "TRANSIENT";
}