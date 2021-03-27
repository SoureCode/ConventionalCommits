<?php

namespace SoureCode\ConventionalCommits\Git;

use DateTime;
use DateTimeImmutable;

class GitCommit
{
    private string $hash;

    private string $author;

    private DateTimeImmutable $authorDate;

    private string $committer;

    private DateTimeImmutable $committerDate;

    private string $subject;

    private string $body;

    public function __construct(
        string $hash,
        string $author,
        string $authorDate,
        string $committer,
        string $committerDate,
        string $subject,
        string $body
    ) {
        $this->hash = $hash;
        $this->author = $author;
        $this->authorDate = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $authorDate);
        $this->committer = $committer;
        $this->committerDate = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $committerDate);
        $this->subject = $subject;
        $this->body = $body;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthorDate(): DateTimeImmutable
    {
        return $this->authorDate;
    }

    public function getCommitter(): string
    {
        return $this->committer;
    }

    public function getCommitterDate(): DateTimeImmutable
    {
        return $this->committerDate;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
