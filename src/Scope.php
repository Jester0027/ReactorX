<?php

namespace Jester0027\Phuck;

/**
 * Enumeration of component scopes
 */
enum Scope: string
{
    /**
     * Instantiated once at the application startup<br>
     * Only used for configuration
     */
    case Startup = "STARTUP";
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