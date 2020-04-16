<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action_hooks
// Instance of class
{
    public static $hooks_instance;
    public static $actions;
    public static $current_action;
    public static $run_actions;

    public static function instance()
    {
        if (!self::$hooks_instance) {
            self::$hooks_instance = new Action_hooks();
        }
        return self::$hooks_instance;
    }

    /**
     * Add Action
     *
     * Add a new hook trigger action
     *
     * @param mixed $name
     * @param mixed $function
     * @param mixed $priority
     */
    public function add_action($name, $function, $priority = 10)
    {
        // If we have already registered this action return true
        if (isset(self::$actions[$name][$priority][$function])) {
            return true;
        }
        /**
         * Allows us to iterate through multiple action hooks.
         */
        if (is_array($name)) {
            foreach ($name AS $name) {
                // Store the action hook in the $hooks array
                self::$actions[$name][$priority][$function] = array(
                    "function" => $function
                );
            }
        } else {
            // Store the action hook in the $hooks array
            self::$actions[$name][$priority][$function] = array(
                "function" => $function
            );
        }
        return true;
    }

    /**
     * Do Action
     *
     * Trigger an action for a particular action hook
     *
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public function do_action($name, $arguments = "")
    {
        // Oh, no you didn't. Are you trying to run an action hook that doesn't exist?
        if (!isset(self::$actions[$name])) {
            return $arguments;
        }
        // Set the current running hook to this
        self::$current_action = $name;
        // Key sort our action hooks
        ksort(self::$actions[$name]);
        foreach (self::$actions[$name] AS $priority => $names) {
            if (is_array($names)) {
                foreach ($names AS $name) {
                    $return = call_user_func_array($name['function'], array(
                        &$arguments
                    ));
                    if ($return) {
                        $arguments = $return;
                    }
                    self::$run_actions[$name][$priority];
                }
            }
        }
        self::$current_action = '';
        return $arguments;
    }

    /**
     * Remove Action
     *
     * Remove an action hook. No more needs to be said.
     *
     * @param mixed $name
     * @param mixed $function
     * @param mixed $priority
     */
    public function remove_action($name, $function, $priority = 10)
    {
        // If the action hook doesn't, just return true
        if (!isset(self::$actions[$name][$priority][$function])) {
            return true;
        }
        // Remove the action hook from our hooks array
        unset(self::$actions[$name][$priority][$function]);
    }

    /**
     * Current Action
     *
     * Get the currently running action hook
     *
     */
    public function current_action()
    {
        return self::$current_action;
    }

    /**
     * Has Run
     *
     * Check if a particular hook has been run
     *
     * @param mixed $hook
     * @param mixed $priority
     */
    public function has_run($action, $priority = 10)
    {
        if (isset(self::$actions[$action][$priority])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Action Exists
     *
     * Does a particular action hook even exist?
     *
     * @param mixed $name
     */
    public function action_exists($name)
    {
        if (isset(self::$actions[$name])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Will print our information about all Action_hooks and actions
     * neatly presented to the user.
     *
     */
    public static function debug_actions()
    {
        if (isset(self::$actions)) {
            echo "<h2>Registered action hooks</h2>";
            echo "<pre>";
            print_r(self::$actions);
            echo "</pre>";
            echo "<br />";
        }
        if (isset(self::$run_actions)) {
            echo "<h2>Previously run action hooks</h2>";
            echo "<p>Hooks that have been called previously.</p>";
            echo "<pre>";
            print_r(self::$run_actions);
            echo "</pre>";
            echo "<br />";
        }
    }
}

/**
 * Add a new action hook
 *
 * @param mixed $name
 * @param mixed $function
 * @param mixed $priority
 */
function add_action($name, $function, $priority = 10)
{
    return Action_hooks::instance()->add_action($name, $function, $priority);
}

/**
 * Run an action
 *
 * @param mixed $name
 * @param mixed $arguments
 * @return mixed
 */
function do_action($name, $arguments = "")
{
    return Action_hooks::instance()->do_action($name, $arguments);
}

/**
 * Remove an action
 *
 * @param mixed $name
 * @param mixed $function
 * @param mixed $priority
 */
function remove_action($name, $function, $priority = 10)
{
    return Action_hooks::instance()->remove_action($name, $function, $priority);
}

/**
 * Check if an action exists
 *
 * @param mixed $name
 */
function action_exists($name)
{
    return Action_hooks::instance()->action_exists($name);
}

/**
 * Return the number of Action_hooks found
 *
 */
/**
 * Debug function will return all Action_hooks registered
 *
 */
function debug_class()
{
    Action_hooks::debug_class();
}
