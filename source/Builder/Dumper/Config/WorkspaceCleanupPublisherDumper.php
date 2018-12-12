<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

use CodedMonkey\Jenkins\Builder\Config\WorkspaceCleanupPublisher;

class WorkspaceCleanupPublisherDumper
{
    public function dump(\DOMDocument $dom, WorkspaceCleanupPublisher $publisher)
    {
        $node = $dom->createElement('hudson.plugins.ws__cleanup.WsCleanup');
        $node->setAttribute('plugin', 'ws-cleanup');

        $patterns = array_merge(
            array_map(function($pattern) {
                return ['include', $pattern];
            }, $publisher->getIncludePatterns()),
            array_map(function($pattern) {
                return ['exclude', $pattern];
            }, $publisher->getExcludePatterns())
        );

        if (count($patterns) > 0) {
            $patternsNode = $dom->createElement('patterns');
            $node->appendChild($patternsNode);

            foreach ($patterns as $pattern) {
                $patternNode = $dom->createElement('hudson.plugins.ws__cleanup.Pattern');
                $patternsNode->appendChild($patternNode);

                $patternTextNode = $dom->createElement('pattern', $pattern[0]);
                $patternNode->appendChild($patternTextNode);

                $patternTypeNode = $dom->createElement('type', strtoupper($pattern[1]));
                $patternNode->appendChild($patternTypeNode);
            }
        }
        else {
            $patternsNode = $dom->createElement('patterns');
            $patternsNode->setAttribute('class', 'empty-list');
            $node->appendChild($patternsNode);
        }

        $deleteDirectoriesNode = $dom->createElement('deleteDirs', $publisher->getMatchDirectories() ? 'true' : 'false');
        $node->appendChild($deleteDirectoriesNode);

        // todo make configurable
        $skipNode = $dom->createElement('skipWhenFailed', 'false');
        $node->appendChild($skipNode);

        static $buildStates = ['success', 'unstable', 'failure', 'notBuilt', 'aborted'];

        $cleanStates = $publisher->getCleanWhen();
        foreach ($buildStates as $buildState) {
            $cleanState = $cleanStates[$buildState] ?? true;

            $cleanNode = $dom->createElement(sprintf('cleanWhen%s', ucfirst($buildState)), $cleanState ? 'true' : 'false');
            $node->appendChild($cleanNode);
        }

        $failNode = $dom->createElement('notFailBuild', $publisher->getFailBuild() ? 'false' : 'true');
        $node->appendChild($failNode);

        $cleanupParentNode = $dom->createElement('cleanupMatrixParent', $publisher->getCleanupParent() ? 'true' : 'false');
        $node->appendChild($cleanupParentNode);

        $externalCommandNode = $dom->createElement('externalDelete', $publisher->getExternalCommand());
        $node->appendChild($externalCommandNode);

        $deferredWipeoutNode = $dom->createElement('deferredWipeout', $publisher->getDeferredWipeout() ? 'true' : 'false');
        $node->appendChild($deferredWipeoutNode);

        return $node;
    }
}
