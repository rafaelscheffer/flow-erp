<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar_url')
            ->label(__('Avatar'))
            ->avatar()
            ->disk('public')
            ->directory('avatars')
            ->visibility('public');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getAvatarFormComponent(),
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getCurrentPasswordFormComponent(),
        ]);
    }
}
