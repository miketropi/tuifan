<?php
/**
 * Starter editorial content: the four reader guides seeded by `wp tuifan setup`.
 *
 * Generated once from the reference reader guides and stored here as real core block
 * documents (Heading, Paragraph, List, Quote, Code, Table, Details, Separator).
 * Required only from the WP-CLI setup path in `inc/starter-site.php`.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Starter article documents, keyed by post slug.
 *
 * @return array<string, array{ title: string, excerpt: string, category: string, tags: list<string>, content: string }>
 */
function tuifan_get_starter_articles(): array {
	return array(
		'agent-start'       => array(
			'title'    => 'Your first AI agent: start small, stay in control.',
			'excerpt'  => 'A reader’s guide to useful tasks, clear briefs, and reviewing the result before taking action.',
			'category' => 'getting-started',
			'tags'     => array( 'agents', 'basics' ),
			'content'  => <<<'BLOCKS'
<!-- wp:paragraph {"className":"tuifan-article__summary"} -->
<p class="tuifan-article__summary">An AI agent can plan steps and use tools to pursue a goal. That makes it useful for multi-step work—and makes your instructions, permissions, and review process more important than ever.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"understand"} -->
<h2 class="wp-block-heading" id="understand">Understand what an agent actually does</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A chatbot usually responds to a message. An agent may also search files, call an API, edit a document, or run a command. Products vary: some require approval for each action, while others can continue until they reach a stopping condition.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Think of an agent as a capable assistant with an incomplete picture, not an accountable owner. You still decide what good work looks like and which actions it is allowed to take.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Give the agent a bounded task, a source of truth, and a clear reason to stop.</p>
<!-- /wp:paragraph -->
</blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":3,"anchor":"good-first-task"} -->
<h3 class="wp-block-heading" id="good-first-task">Choose a reversible first task</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Start with a draft or a read-only analysis. Summarizing meeting notes, comparing documents, and explaining a codebase are easier to inspect than sending emails or changing production systems.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"brief"} -->
<h2 class="wp-block-heading" id="brief">Write a brief the agent can follow</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A useful brief includes five things: the goal, input material, constraints, output format, and acceptance checks. Specificity helps the agent choose the next step instead of guessing your intent.</p>
<!-- /wp:paragraph -->

<!-- wp:code -->
<pre class="wp-block-code"><code>Goal: turn these meeting notes into a decision summary.
Input: the attached notes only.
Constraints: do not infer owners or deadlines.
Output: decisions, open questions, and proposed next steps.
Check: cite the note supporting each decision.
Stop: return a draft; do not send or publish it.</code></pre>
<!-- /wp:code -->

<!-- wp:heading {"level":3,"anchor":"missing-information"} -->
<h3 class="wp-block-heading" id="missing-information">Make uncertainty explicit</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ask the agent to mark missing information rather than fill the gap. A useful phrase is <code>“If the input does not support a claim, label it unknown.”</code> This does not guarantee correctness; it gives you a clearer review surface.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"workflow"} -->
<h2 class="wp-block-heading" id="workflow">Use a plan → draft → review workflow</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li><strong>Plan:</strong> ask for the proposed steps and tools before granting access.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Draft:</strong> work in a copy, branch, or sandbox where changes are reversible.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Review:</strong> compare the result with the source, not just the agent’s explanation.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Approve:</strong> authorize external actions separately from creating the draft.</li>
<!-- /wp:list-item -->
</ol>
<!-- /wp:list -->

<!-- wp:heading {"level":2,"anchor":"choose-mode"} -->
<h2 class="wp-block-heading" id="choose-mode">Choose the right level of autonomy</h2>
<!-- /wp:heading -->

<!-- wp:table -->
<figure class="wp-block-table"><table><caption>Match access to the consequence of a mistake</caption><thead><tr><th>Task</th><th>Starting mode</th><th>Your check</th></tr></thead><tbody><tr><td>Summarize a document</td><td>Read-only</td><td>Compare claims with source passages</td></tr><tr><td>Edit a local file</td><td>Approval before writes</td><td>Inspect the diff and keep a backup</td></tr><tr><td>Send a message</td><td>Draft only</td><td>Confirm recipients and exact wording</td></tr><tr><td>Change a live system</td><td>Separate controlled workflow</td><td>Require rollback and human approval</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading {"level":2,"anchor":"check-result"} -->
<h2 class="wp-block-heading" id="check-result">Check the result before you trust it</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A fluent answer is not evidence. Verify names, dates, calculations, citations, and any action the agent says it performed. Open the cited source. Look at the actual file or tool log.</p>
<!-- /wp:paragraph -->

<!-- wp:quote {"className":"tuifan-callout"} -->
<blockquote class="wp-block-quote tuifan-callout"><!-- wp:paragraph -->
<p><strong>Before enabling tools</strong></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>Keep credentials, private customer data, and production access out of your first experiment. Read the tool’s data-handling policy before uploading sensitive material.</p>
<!-- /wp:paragraph -->
</blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":3,"anchor":"review-checklist"} -->
<h3 class="wp-block-heading" id="review-checklist">A reusable review checklist</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><input type="checkbox" disabled checked aria-label="The task has a defined output"> The task has a defined output.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><input type="checkbox" disabled aria-label="Claims checked against original sources"> Claims checked against original sources.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><input type="checkbox" disabled aria-label="Changes are reversible"> Changes are reversible.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><input type="checkbox" disabled aria-label="External actions explicitly approved"> External actions explicitly approved.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2,"anchor":"next-step"} -->
<h2 class="wp-block-heading" id="next-step">Your next step</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Pick one recurring task. Write a brief, give the agent only the inputs it needs, and review one complete run. Expand its access only after you understand its failure modes.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:paragraph -->
<p><small>Further reading: <a href="https://www.anthropic.com/engineering/building-effective-agents" target="_blank" rel="noopener noreferrer">Building effective agents ↗</a>. This guide is tool-independent; available capabilities and privacy settings differ by product.</small></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"tuifan-starter-note"} -->
<p class="tuifan-starter-note">Starter editorial content for this site. Examples are illustrative, not measured product claims.</p>
<!-- /wp:paragraph -->
BLOCKS
			,
		),
		'agent-permissions' => array(
			'title'    => 'Give your agent a task. Not the keys to everything.',
			'excerpt'  => 'Practical permission boundaries, approval gates, and a way back when something goes wrong.',
			'category' => 'safety',
			'tags'     => array( 'permissions', 'privacy' ),
			'content'  => <<<'BLOCKS'
<!-- wp:paragraph {"className":"tuifan-article__summary"} -->
<p class="tuifan-article__summary">The most useful safety setting is a smaller scope. Let an agent do what the task needs, not everything your account can do.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"map-access"} -->
<h2 class="wp-block-heading" id="map-access">Map the access your task needs</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Write down which files, services, and actions the task requires. Reading a report does not require permission to delete it. Drafting a reply does not require permission to send it.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"anchor":"permission-levels"} -->
<h3 class="wp-block-heading" id="permission-levels">Separate reading, writing, and acting</h3>
<!-- /wp:heading -->

<!-- wp:table -->
<figure class="wp-block-table"><table><caption>A simple access inventory</caption><thead><tr><th>Capability</th><th>Risk</th><th>Safer default</th></tr></thead><tbody><tr><td>Read files</td><td>Exposure of private information</td><td>Share a selected folder or sanitized copy</td></tr><tr><td>Edit files</td><td>Unwanted changes</td><td>Use a branch and inspect the diff</td></tr><tr><td>Run commands</td><td>System or network side effects</td><td>Restrict tools and require approval</td></tr><tr><td>Send or publish</td><td>Irreversible external impact</td><td>Draft first; approve separately</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading {"level":2,"anchor":"approval"} -->
<h2 class="wp-block-heading" id="approval">Create meaningful approval gates</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>An approval prompt is useful only when you can understand the proposed action. Ask to see the destination, parameters, affected files, and expected side effects. Do not approve a long command simply because the agent calls it safe.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Approve the actual action—not a reassuring description of the action.</p>
<!-- /wp:paragraph -->
</blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":3,"anchor":"approval-brief"} -->
<h3 class="wp-block-heading" id="approval-brief">Put the boundary in the brief</h3>
<!-- /wp:heading -->

<!-- wp:code -->
<pre class="wp-block-code"><code>Read only the files in the shared research folder.
Do not run commands, access credentials, or send messages.
If the task needs additional access, explain why and stop.
Return a proposed change list before editing anything.</code></pre>
<!-- /wp:code -->

<!-- wp:heading {"level":2,"anchor":"untrusted"} -->
<h2 class="wp-block-heading" id="untrusted">Treat retrieved content as untrusted input</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A website, email, or document may contain instructions aimed at the agent. Those instructions should not override your task. This is the core concern behind prompt injection: content being mistaken for authority.</p>
<!-- /wp:paragraph -->

<!-- wp:quote {"className":"tuifan-callout"} -->
<blockquote class="wp-block-quote tuifan-callout"><!-- wp:paragraph -->
<p><strong>Instructions are not a security boundary</strong></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>“Ignore malicious instructions” can help communicate intent, but it cannot replace restricted permissions, isolated tools, or approval checks.</p>
<!-- /wp:paragraph -->
</blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":2,"anchor":"recovery"} -->
<h2 class="wp-block-heading" id="recovery">Plan a way back</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Keep an original copy before editing.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Use narrowly scoped, revocable access when supported.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Record which actions were approved and what changed.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Know how to revoke tokens and stop an active run.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2,"anchor":"permission-checklist"} -->
<h2 class="wp-block-heading" id="permission-checklist">Before the next run</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Check the tool’s settings, not just your prompt. Confirm that approval mode is enabled, the allowed folder is correct, and no unrelated integrations are connected.</p>
<!-- /wp:paragraph -->

<!-- wp:details -->
<details class="wp-block-details"><summary>What if the agent needs more access?</summary><!-- wp:paragraph -->
<p>Pause the task. Decide whether the additional capability is necessary, grant the smallest scope available, and run the next step under review. Avoid upgrading every permission to solve one blocked action.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:heading {"level":2,"anchor":"security-reading"} -->
<h2 class="wp-block-heading" id="security-reading">Further reading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="https://genai.owasp.org/llmrisk/llm01-prompt-injection/" target="_blank" rel="noopener noreferrer">OWASP: prompt injection ↗</a> explains the threat in more depth. For sensitive workflows, involve your organization’s security team.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"tuifan-starter-note"} -->
<p class="tuifan-starter-note">Starter editorial content for this site. Examples are illustrative, not measured product claims.</p>
<!-- /wp:paragraph -->
BLOCKS
			,
		),
		'agent-context'     => array(
			'title'    => 'Better context, better answers.',
			'excerpt'  => 'Build a focused source pack, make uncertainty visible, and keep a clean handoff between sessions.',
			'category' => 'workflows',
			'tags'     => array( 'context', 'prompts' ),
			'content'  => <<<'BLOCKS'
<!-- wp:paragraph {"className":"tuifan-article__summary"} -->
<p class="tuifan-article__summary">Better context is not always more context. A small, relevant source pack is often easier to review than an entire workspace.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"source-pack"} -->
<h2 class="wp-block-heading" id="source-pack">Build a source pack</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Include the task brief, current source documents, terminology, and examples of the expected output. Keep unrelated history out. If documents conflict, say which one takes precedence.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"anchor":"source-order"} -->
<h3 class="wp-block-heading" id="source-order">Define a source of truth</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A helpful order is: current approved policy, current project documents, then background material. Ask the agent to flag conflicts rather than quietly choose a version.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"context-file"} -->
<h2 class="wp-block-heading" id="context-file">Use a plain-text context file</h2>
<!-- /wp:heading -->

<!-- wp:code -->
<pre class="wp-block-code"><code># Task context
## Goal
Compare the two proposals against the requirements.
## Source of truth
requirements.md is authoritative for this task.
## Boundaries
Do not infer pricing or capabilities not in the proposals.
## Output
A comparison table with source references and open questions.
## Stop condition
Return the comparison; do not contact either vendor.</code></pre>
<!-- /wp:code -->

<!-- wp:heading {"level":2,"anchor":"attachments"} -->
<h2 class="wp-block-heading" id="attachments">Prepare files before attaching them</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li>Remove secrets and unnecessary personal information.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Use descriptive filenames and mark outdated versions.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Check that scanned documents have readable text.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Include enough surrounding material to interpret quotations.</li>
<!-- /wp:list-item -->
</ol>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>For a long document, start by asking the agent to identify the relevant sections. Then verify those sections yourself before using the summary to make a decision.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"anchor":"context-limits"} -->
<h3 class="wp-block-heading" id="context-limits">Expect context limits</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Tools may truncate documents or compact earlier messages. If a crucial requirement matters, keep it in the task brief and check that the final output follows it. Do not assume every prior message remains available.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"citations"} -->
<h2 class="wp-block-heading" id="citations">Ask for useful citations</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>“Include sources” is weaker than “cite the filename and section supporting each conclusion.” The latter makes review practical. A citation still needs checking: the source may be irrelevant or the interpretation may be wrong.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Context helps the model reason. Provenance helps you review that reasoning.</p>
<!-- /wp:paragraph -->
</blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":2,"anchor":"handoff"} -->
<h2 class="wp-block-heading" id="handoff">Create a clean handoff</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>When a session becomes long, ask for a handoff with confirmed decisions, unresolved questions, files changed, and next steps. Inspect it before starting a new session.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"context-takeaway"} -->
<h2 class="wp-block-heading" id="context-takeaway">Try this today</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Create one <code>context.md</code> for your next task. Keep the goal, sources, and boundaries visible. Add context when a specific question needs it, not just because the agent can accept more text.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"tuifan-starter-note"} -->
<p class="tuifan-starter-note">Starter editorial content for this site. Examples are illustrative, not measured product claims.</p>
<!-- /wp:paragraph -->
BLOCKS
			,
		),
		'agent-evaluation'  => array(
			'title'    => 'How to tell if an AI agent is actually helping.',
			'excerpt'  => 'Test everyday tasks, check the evidence, and track failures instead of trusting a polished demo.',
			'category' => 'workflows',
			'tags'     => array( 'evaluation', 'reliability' ),
			'content'  => <<<'BLOCKS'
<!-- wp:paragraph {"className":"tuifan-article__summary"} -->
<p class="tuifan-article__summary">An impressive demonstration tells you what can go right. A small repeatable test tells you whether an agent fits your everyday work.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"real-tasks"} -->
<h2 class="wp-block-heading" id="real-tasks">Choose representative tasks</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Use examples from work you understand well enough to judge. Include a straightforward case, a messy input, a missing-information case, and a task where the correct behavior is to stop and ask.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Sanitize private material before testing a new service. Keep the same inputs when comparing settings or tools.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"rubric"} -->
<h2 class="wp-block-heading" id="rubric">Define success before running the test</h2>
<!-- /wp:heading -->

<!-- wp:table -->
<figure class="wp-block-table"><table><caption>A qualitative rubric you can adapt</caption><thead><tr><th>Criterion</th><th>Pass condition</th><th>Evidence</th></tr></thead><tbody><tr><td>Accuracy</td><td>Claims agree with the sources</td><td>Checked quotations and calculations</td></tr><tr><td>Completeness</td><td>Required output sections are present</td><td>Task checklist</td></tr><tr><td>Boundaries</td><td>No unapproved actions</td><td>Action log and changed files</td></tr><tr><td>Uncertainty</td><td>Missing facts are marked as unknown</td><td>Answer and source comparison</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading {"level":3,"anchor":"avoid-score"} -->
<h3 class="wp-block-heading" id="avoid-score">Do not hide failures behind one score</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A useful summary should preserve the failure type. An agent that writes a polished report but invents a citation has not solved the task. Record accuracy and permission failures separately.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"test-record"} -->
<h2 class="wp-block-heading" id="test-record">Keep a simple test record</h2>
<!-- /wp:heading -->

<!-- wp:code -->
<pre class="wp-block-code"><code>{
  "task": "Compare two proposals",
  "tool_and_settings": "record the tool and configuration",
  "input_version": "sanitized-input-v1",
  "checks": {
    "claims_supported": null,
    "required_sections_present": null,
    "permissions_respected": null
  },
  "review_notes": "Complete after reviewing the run"
}</code></pre>
<!-- /wp:code -->

<!-- wp:paragraph -->
<p>The <code>null</code> values mean “not yet reviewed.” They are not performance results.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"anchor":"repeat"} -->
<h2 class="wp-block-heading" id="repeat">Repeat before expanding autonomy</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Outputs can vary across runs. Repeat important tasks and inspect differences. Record time spent reviewing and correcting, not just how quickly the first answer appeared.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"anchor":"failure-patterns"} -->
<h3 class="wp-block-heading" id="failure-patterns">Look for failure patterns</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Does the agent invent a missing detail?</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Does it keep working after the stop condition?</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Does it cite a source that does not support the claim?</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Does it silently skip difficult parts of the request?</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2,"anchor":"improve"} -->
<h2 class="wp-block-heading" id="improve">Change one thing at a time</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Try a clearer brief, a smaller source pack, or a tighter permission boundary. Rerun the same examples. This makes it easier to tell which change actually helped.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2,"anchor":"evaluation-next"} -->
<h2 class="wp-block-heading" id="evaluation-next">Your next step</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Save a few representative inputs and a review checklist. Use them whenever the model, tool, or workflow changes. The goal is not to prove the agent is perfect; it is to understand where you can rely on it and where you need a stronger check.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"tuifan-starter-note"} -->
<p class="tuifan-starter-note">Starter editorial content for this site. Examples are illustrative, not measured product claims.</p>
<!-- /wp:paragraph -->
BLOCKS
			,
		),
	);
}
