<?php

namespace TenantCloud\Cors\Profile;

/**
 * A CORS profile based on config values.
 */
class ConfigCorsProfile extends AbstractCorsProfile
{
	/** @var string */
	protected $profile;

	public function __construct(string $profile)
	{
		$this->profile = $profile;
	}

	/**
	 * @inheritDoc
	 */
	public function allowCredentials(): bool
	{
		return config($this->configKey('allow_credentials')) ?? false;
	}

	/**
	 * @inheritDoc
	 */
	public function allowOrigins(): array
	{
		return config($this->configKey('allow_origins')) ?? [];
	}

	/**
	 * @inheritDoc
	 */
	public function allowMethods(): array
	{
		return config($this->configKey('allow_methods')) ?? [];
	}

	/**
	 * @inheritDoc
	 */
	public function allowHeaders(): array
	{
		return config($this->configKey('allow_headers')) ?? [];
	}

	/**
	 * @inheritDoc
	 */
	public function exposeHeaders(): array
	{
		return config($this->configKey('expose_headers')) ?? [];
	}

	/**
	 * @inheritDoc
	 */
	public function maxAge(): int
	{
		return config($this->configKey('max_age')) ?? 0;
	}

	/**
	 * Returns config key for this profile.
	 */
	protected function configKey(string $path): string
	{
		return "cors.profiles.{$this->profile}.{$path}";
	}
}
