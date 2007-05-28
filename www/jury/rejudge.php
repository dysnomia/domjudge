<?php
/**
 * $Id$
 * Marks a set of submissions for rejudging, limited by key=value
 * key has to be a full quantifier, e.g. "submission.teamid"
 *
 * $key must be one of (judging.judgehost, submission.teamid, submission.probid,
 * submission.langid, submission.submitid)
 */

require('init.php');

/** These are the tables that we can deal with */
$tablemap = array (
	'judgehost'  => 'judging.judgehost',
	'language'   => 'submission.langid',
	'problem'    => 'submission.probid',
	'submission' => 'submission.submitid',
	'team'       => 'submission.teamid'
	);

$table = @$_POST['table'];
$id    = @$_POST['id'];

if ( empty($table) || empty($id) ) {
	error("no table or id passed for selection in rejudging");
} elseif ( !isset($tablemap[$table]) ) {
	error("unknown table in rejudging");
}

global $DB;

$cid = getCurContest();


// This can be done in one Update from MySQL 4.0.4 and up, but that wouldn't
// allow us to call calcScoreRow() for the right rows, so we'll just loop
// over the results one at a time.
$res = $DB->q('SELECT * FROM judging
	       LEFT JOIN submission USING (submitid)
	       WHERE judging.cid = %i AND valid = 1 AND
	       ( result IS NULL OR result != "correct" ) AND ' .
	       $tablemap[$table] . ' = %s',
	      $cid, $id);

while ( $jud = $res->next() ) {
	$DB->q('START TRANSACTION');
	
	$DB->q('UPDATE judging SET valid = 0 WHERE judgingid = %i',
	       $jud['judgingid']);

	$DB->q('UPDATE submission SET judgehost = NULL, judgemark = NULL
		WHERE submitid = %i', $jud['submitid']);

	calcScoreRow($cid, $jud['teamid'], $jud['probid']);
	$DB->q('COMMIT');
}


/** redirect back. */
header('Location: '.getBaseURI().'jury/'.$table.'.php?id='.urlencode($id));
