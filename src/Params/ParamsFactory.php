<?php namespace DBDiff\Params;

use DBDiff\Exceptions\CLIException;


class ParamsFactory {

    public static function get() {

        $params = new DefaultParams;

        $cli = new CLIGetter;
        $paramsCLI = $cli->getParams();

        if (!isset($paramsCLI->debug)) {
            error_reporting(E_ERROR);
        }

        $fs = new FSGetter($paramsCLI);
        $paramsFS = $fs->getParams();

        $params = self::merge($params, $paramsFS);
        $params = self::merge($params, $paramsCLI);

        if (empty($params->server1)) {
            throw new CLIException("A server is required");
        }

        if (!isset($params->input['kind']) || !in_array($params->input['kind'], ["db", "table"])) {
            throw new CLIException("Operation type not defined");
        }

        if (!isset($params->input['source']['server'])) {
            throw new CLIException("Server source not defined");
        }

        if (!isset($params->input['target']['server'])) {
            throw new CLIException("Server target not defined");
        }

        if (!isset($params->input['source']['db'])) {
            throw new CLIException("DB source not defined");
        }

        if (!isset($params->input['target']['db'])) {
            throw new CLIException("DB target not defined");
        }

        return $params;
    }

    protected function merge($obj1, $obj2) {
        return (object) array_merge((array) $obj1, (array) $obj2);
    }
}
