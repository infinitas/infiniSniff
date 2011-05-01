<?php
if (class_exists('Generic_Sniffs_WhiteSpace_ScopeIndentSniff', true) === false) {
    $error = 'Class Generic_Sniffs_WhiteSpace_ScopeIndentSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

class Cake_Sniffs_WhiteSpace_ScopeIndentSniff extends Generic_Sniffs_WhiteSpace_ScopeIndentSniff
{

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_ELSE) {
            $next = $phpcsFile->findNext(
                PHP_CodeSniffer_Tokens::$emptyTokens,
                ($stackPtr + 1),
                null,
                true
            );

            if ($tokens[$next]['code'] === T_IF) {
                return;
            }
        }

        $firstToken = $stackPtr;
        for ($i = $stackPtr; $i >= 0; $i--) {
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                $firstToken = $i;
            }

            if ($tokens[$i]['column'] === 1) {
                break;
            }
        }

        $expectedIndent = $this->calculateExpectedIndent($tokens, $firstToken);

        if ($tokens[$firstToken]['column'] !== $expectedIndent) {
            $error = 'Line indented incorrectly; expected %s spaces, found %s';
            $data  = array(
                      ($expectedIndent - 1),
                      ($tokens[$firstToken]['column'] - 1),
                     );
            $phpcsFile->addError($error, $stackPtr, 'Incorrect', $data);
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        if (in_array($tokens[$firstToken]['code'], $this->nonIndentingScopes) === false) {
            $indent = ($expectedIndent + $this->indent);
        } else {
            $indent = $expectedIndent;
        }

        $newline     = false;
        $commentOpen = false;
        $inHereDoc   = false;

        for ($i = ($scopeOpener + 1); $i < $scopeCloser; $i++) {

            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$scopeOpeners) === true) {
                if (isset($tokens[$i]['scope_opener']) === true) {
                    $i = $tokens[$i]['scope_closer'];

                    $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($i + 1), null, true);
                    if ($tokens[$nextToken]['code'] === T_SEMICOLON) {
                        $i = $nextToken;
                    }
                } else {
                    $nextToken = $phpcsFile->findNext(T_SEMICOLON, $i, $scopeCloser);
                    if ($nextToken !== false) {
                        $i = $nextToken;
                    }
                }

                continue;
            }

            if ($tokens[$i]['code'] === T_START_HEREDOC) {
                $inHereDoc = true;
                continue;
            } else if ($inHereDoc === true) {
                if ($tokens[$i]['code'] === T_END_HEREDOC) {
                    $inHereDoc = false;
                }

                continue;
            }

            if ($tokens[$i]['column'] === 1) {
                $newline = true;
            }

            if ($newline === true && $tokens[$i]['code'] !== T_WHITESPACE) {
                $newline    = false;
                $firstToken = $i;

                $column = $tokens[$firstToken]['column'];

                if ($tokens[$firstToken]['code'] === T_INLINE_HTML) {
                    $trimmedContentLength
                        = strlen(ltrim($tokens[$firstToken]['content']));
                    if ($trimmedContentLength === 0) {
                        continue;
                    }

                    $contentLength = strlen($tokens[$firstToken]['content']);
                    $column        = ($contentLength - $trimmedContentLength + 1);
                }

                if (in_array($tokens[$firstToken]['code'], PHP_CodeSniffer_Tokens::$stringTokens) === true) {
                    if (in_array($tokens[($firstToken - 1)]['code'], PHP_CodeSniffer_Tokens::$stringTokens) === true) {
                        continue;
                    }
                }

                $comments = array(
                             T_COMMENT,
                             T_DOC_COMMENT
                            );

                if (in_array($tokens[$firstToken]['code'], $comments) === true) {
                    $content = trim($tokens[$firstToken]['content']);
                    if (preg_match('|^/\*|', $content) !== 0) {
                        if (preg_match('|\*/$|', $content) === 0) {
                            $commentOpen = true;
                        }
                    } else if ($commentOpen === true) {
                        if ($content === '') {
                            continue;
                        }

                        $contentLength = strlen($tokens[$firstToken]['content']);
                        $trimmedContentLength
                            = strlen(ltrim($tokens[$firstToken]['content']));

                        $column = ($contentLength - $trimmedContentLength + 1);
                        if (preg_match('|\*/$|', $content) !== 0) {
                            $commentOpen = false;
                        }
                    }//end if

                    /* CakeStart - doc blocks are not indented */
                    if ($tokens[$firstToken]['code'] === T_DOC_COMMENT) {
                        $indent = 0;
                    }
                    /* CakeEnd */
                }//end if

                if ($column !== $indent) {
                    if ($this->exact === true || $column < $indent) {
                        $type  = 'IncorrectExact';
                        $error = 'Line indented incorrectly; expected ';
                        if ($this->exact === false) {
                            $error .= 'at least ';
                            $type   = 'Incorrect';
                        }

                        $error .= '%s spaces, found %s';
                        $data = array(
                                  ($indent - 1),
                                  ($column - 1),
                                );
                        $phpcsFile->addError($error, $firstToken, $type, $data);
                    }
                }//end if
            }//end if
        }//end for

    }
}//end class

?>
