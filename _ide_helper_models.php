<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $patronymic_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $avatar
 * @property string|null $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePatronymicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUpdatedAt($value)
 */
	class Admin extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Chapter
 *
 * @property int $id
 * @property int|null $specialist_id
 * @property string $status
 * @property string|null $type
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist|null $specialist
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereUpdatedAt($value)
 */
	class Chapter extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Education
 *
 * @property int $id
 * @property string $status
 * @property int $specialist_id
 * @property string|null $level
 * @property string $institution
 * @property string $faculty
 * @property string $specialty
 * @property \Illuminate\Support\Carbon $graduation_at
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $diploma
 * @property-read int|null $diploma_count
 * @property-read array $diploma_media
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Specialist $specialist
 * @method static \Illuminate\Database\Eloquent\Builder|Education newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Education newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Education query()
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereFaculty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereGraduationAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereInstitution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereSpecialty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereUpdatedAt($value)
 */
	class Education extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $status
 * @property string $name
 * @property string|null $description
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $image_url
 * @property-read bool $is_muted
 * @property-read \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null $last_message
 * @property-read int $member_count
 * @property-read int $message_count
 * @property-read int $unread_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessage> $groupMessages
 * @property-read int|null $group_messages_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media|null $image
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMember> $members
 * @property-read int|null $members_count
 * @property-read \App\Models\Program|null $program
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUserId($value)
 */
	class Group extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\GroupMember
 *
 * @property int $id
 * @property string $status
 * @property int|null $group_id
 * @property int|null $user_id
 * @property int $is_admin
 * @property int|null $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMember whereUserId($value)
 */
	class GroupMember extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GroupMessage
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $group_id
 * @property string|null $message
 * @property int|null $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media|null $fileMedia
 * @property-read null $file
 * @property-read \App\Models\Group|null $group
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessageEvent> $groupMessageEvents
 * @property-read int|null $group_message_events_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage activeMessages()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessage whereUserId($value)
 */
	class GroupMessage extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\GroupMessageEvent
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $group_message_id
 * @property int|null $is_read
 * @property int|null $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GroupMessage|null $groupMessage
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent whereGroupMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupMessageEvent whereUserId($value)
 */
	class GroupMessageEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Location
 *
 * @property int $id
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $country
 * @property string|null $federal_district
 * @property string|null $region_type
 * @property string|null $region
 * @property string|null $city
 * @property int|null $popular
 * @property string|null $timezone
 * @property string|null $lat_long
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\News> $news
 * @property-read int|null $news_count
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereFederalDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLatLong($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location wherePopular($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereRegionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereUpdatedAt($value)
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Message
 *
 * @property int $id
 * @property \App\Models\User|null $from
 * @property \App\Models\User|null $to
 * @property string|null $message
 * @property int|null $replay
 * @property int $from_read
 * @property int $to_read
 * @property int $from_deleted
 * @property int $to_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media|null $fileMedia
 * @property-read null $file
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereFromDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereFromRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereReplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereToDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereToRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 */
	class Message extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\MutedGroup
 *
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $muted
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedGroup whereUserId($value)
 */
	class MutedGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MutedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $muted_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $muted
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser whereMutedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MutedUser whereUserId($value)
 */
	class MutedUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\News
 *
 * @property int $id
 * @property string $status_id
 * @property string $title
 * @property int|null $location_id
 * @property string $short_description
 * @property string $description
 * @property int $view_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read string $image_url
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media|null $image
 * @property-read \App\Models\Location|null $location
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\NewsCategory|null $newsCategory
 * @method static \Illuminate\Database\Eloquent\Builder|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News query()
 * @method static \Illuminate\Database\Eloquent\Builder|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereViewCount($value)
 */
	class News extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\NewsCategory
 *
 * @property int $id
 * @property int $news_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereNewsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereUpdatedAt($value)
 */
	class NewsCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Page
 *
 * @property int $id
 * @property string $status
 * @property string|null $title
 * @property string $key
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereUpdatedAt($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Program
 *
 * @property int $id
 * @property int|null $specialist_id
 * @property string|null $presenter
 * @property int|null $presenter_id
 * @property int|null $group_id
 * @property string $status
 * @property int $is_online
 * @property int|null $location_id
 * @property int|null $media_id
 * @property string $name
 * @property float $price
 * @property float|null $sale_price
 * @property string|null $link
 * @property int|null $member_count
 * @property string|null $description
 * @property string|null $time_zone
 * @property float|null $rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramCategory> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\ProgramFavorite|null $favorite
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $gallery
 * @property-read int|null $gallery_count
 * @property-read int $comment_count
 * @property-read array $gallery_media
 * @property-read bool $is_favorite
 * @property-read bool $is_my
 * @property-read \Illuminate\Support\Collection $my_rate
 * @property-read int $notification_count
 * @property-read int $rate_count
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\Location|null $location
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $presenter_user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chapter> $programChapters
 * @property-read int|null $program_chapters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramComment> $programComments
 * @property-read int|null $program_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramComplaint> $programComplaints
 * @property-read int|null $program_complaints_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramDate> $programDates
 * @property-read int|null $program_dates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramRate> $programRates
 * @property-read int|null $program_rates_count
 * @property-read \App\Models\Specialist|null $specialist
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereMemberCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program wherePresenter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program wherePresenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereTimeZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Program whereUpdatedAt($value)
 */
	class Program extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\ProgramCategory
 *
 * @property int $id
 * @property int|null $specialist_id
 * @property string $status
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist|null $specialist
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramCategory whereUpdatedAt($value)
 */
	class ProgramCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramChapter
 *
 * @property int $id
 * @property int|null $program_id
 * @property int|null $chapter_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chapter|null $chapter
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter whereChapterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramChapter whereUpdatedAt($value)
 */
	class ProgramChapter extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramComment
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $program_id
 * @property string|null $message
 * @property int $rate
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Program|null $program
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComment whereUserId($value)
 */
	class ProgramComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramComplaint
 *
 * @property int $id
 * @property int $user_id
 * @property int $program_id
 * @property string $status
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Program $program
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramComplaint whereUserId($value)
 */
	class ProgramComplaint extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramDate
 *
 * @property int $id
 * @property int|null $program_id
 * @property string|null $date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramDate whereUpdatedAt($value)
 */
	class ProgramDate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramFavorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $program_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Program|null $programs
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramFavorite whereUserId($value)
 */
	class ProgramFavorite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramProgramCategory
 *
 * @property int $id
 * @property int $program_id
 * @property int $program_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProgramCategory $category
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory whereProgramCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramProgramCategory whereUpdatedAt($value)
 */
	class ProgramProgramCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramRate
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $program_id
 * @property int $rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Program|null $program
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramRate whereUserId($value)
 */
	class ProgramRate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProgramUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $program_id
 * @property int $is_payed
 * @property int|null $is_seen
 * @property int $is_specialist_seen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereIsPayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereIsSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereIsSpecialistSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProgramUser whereUserId($value)
 */
	class ProgramUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Specialist
 *
 * @property int $id
 * @property string $status
 * @property int $user_id
 * @property string|null $phone
 * @property float|null $rate
 * @property int|null $online
 * @property int|null $offline
 * @property int|null $location_id
 * @property string|null $address
 * @property string|null $link
 * @property string|null $video
 * @property string|null $video_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpecialistClient> $clients
 * @property-read int|null $clients_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Education> $educations
 * @property-read int|null $educations_count
 * @property-read \App\Models\SpecialistFavorite|null $favorite
 * @property-read int $comment_count
 * @property-read array $document
 * @property-read bool $is_favorite
 * @property-read bool $is_my
 * @property-read bool $is_subscribed
 * @property-read int $new_subscribers_count
 * @property-read int $program_count
 * @property-read int $program_notification_count
 * @property-read int|null $subscribers_count
 * @property-read array $video_media
 * @property-read \App\Models\Location|null $location
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> $programs
 * @property-read int|null $programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Specialty> $specialties
 * @property-read int|null $specialties_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpecialistSubscription> $subscribers
 * @property-read \App\Models\User $user
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $videos
 * @property-read int|null $videos_count
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereOffline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialist whereVideoStatus($value)
 */
	class Specialist extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\SpecialistClient
 *
 * @property int $id
 * @property int $specialist_id
 * @property string $status
 * @property int|null $user_id
 * @property string|null $pseudonym
 * @property string|null $email
 * @property string|null $phone
 * @property int|null $verified
 * @property int|null $notified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist $specialist
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereNotified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient wherePseudonym($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistClient whereVerified($value)
 */
	class SpecialistClient extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpecialistComment
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $specialist_id
 * @property string|null $message
 * @property int $rate
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist|null $specialist
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistComment whereUserId($value)
 */
	class SpecialistComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpecialistFavorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $specialist_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist|null $specialists
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistFavorite whereUserId($value)
 */
	class SpecialistFavorite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpecialistRate
 *
 * @property-read \App\Models\Specialist $specialist
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistRate query()
 */
	class SpecialistRate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpecialistSpecialty
 *
 * @property int $id
 * @property int $specialist_id
 * @property int $speciality_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist $specialists
 * @property-read \App\Models\Specialty $specialties
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty whereSpecialityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSpecialty whereUpdatedAt($value)
 */
	class SpecialistSpecialty extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpecialistSubscription
 *
 * @property int $id
 * @property int $user_id
 * @property int $specialist_id
 * @property int $is_new
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist $specialist
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription whereIsNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialistSubscription whereUserId($value)
 */
	class SpecialistSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Specialty
 *
 * @property int $id
 * @property int|null $requested_by
 * @property int|null $requested_by_user
 * @property string $name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist|null $specialist
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereRequestedByUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereUpdatedAt($value)
 */
	class Specialty extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property int|null $status_id
 * @property string $first_name
 * @property string $last_name
 * @property string $patronymic_name
 * @property string|null $email
 * @property string|null $phone
 * @property int|null $location_id
 * @property string|null $address
 * @property string|null $password
 * @property string|null $gender
 * @property \Illuminate\Support\Carbon|null $b_day
 * @property string|null $content
 * @property string|null $url
 * @property int $is_muted
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property string|null $last_visit
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media|null $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserDevice> $devices
 * @property-read int|null $devices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpecialistFavorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read string|null $avatar_url
 * @property-read string $full_name
 * @property-read bool $is_client
 * @property-read string $is_info_added
 * @property-read bool $is_my_specialist
 * @property-read bool $is_user_muted
 * @property-read \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null $last_message
 * @property-read int $message_count
 * @property-read int $program_count
 * @property-read int $subscription_count
 * @property-read int $unread_count_all
 * @property-read int $unread_count
 * @property-read int $unseen_program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMember> $groupMembares
 * @property-read int|null $group_membares_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groupMembersMany
 * @property-read int|null $group_members_many_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessageEvent> $groupMessageEvents
 * @property-read int|null $group_message_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessage> $groupMessageEventsMany
 * @property-read int|null $group_message_events_many_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessage> $groupMessages
 * @property-read int|null $group_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groupMessagesMany
 * @property-read int|null $group_messages_many_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \App\Models\Location|null $location
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messagesFrom
 * @property-read int|null $messages_from_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messagesTo
 * @property-read int|null $messages_to_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSpecialist> $mySpecialists
 * @property-read int|null $my_specialists_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramComment> $programComments
 * @property-read int|null $program_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramFavorite> $programFavorites
 * @property-read int|null $program_favorites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramRate> $programRates
 * @property-read int|null $program_rates_count
 * @property-read \App\Models\ProgramUser|null $programUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> $programs
 * @property-read int|null $programs_count
 * @property-read \App\Models\Specialist|null $specialist
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpecialistComment> $specialistComments
 * @property-read int|null $specialist_comments_count
 * @property-read \App\Models\Specialist|null $specialistIsActive
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpecialistSubscription> $specialistSubscription
 * @property-read int|null $specialist_subscription_count
 * @property-read \App\Models\UserStatus|null $status
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsMuted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastVisit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePatronymicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerifiedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \Spatie\MediaLibrary\HasMedia, \Tymon\JWTAuth\Contracts\JWTSubject {}
}

namespace App\Models{
/**
 * App\Models\UserDevice
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereUserId($value)
 */
	class UserDevice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserSpecialist
 *
 * @property int $id
 * @property string $status
 * @property int $user_id
 * @property int $specialist_id
 * @property string|null $pseudonym
 * @property int $notified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Specialist $specialist
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist whereNotified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist wherePseudonym($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist whereSpecialistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSpecialist whereUserId($value)
 */
	class UserSpecialist extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserStatus
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereName($value)
 */
	class UserStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\VerifyCode
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $email
 * @property string|null $phone
 * @property string $type
 * @property string $token
 * @property string $code
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyCode whereUserId($value)
 */
	class VerifyCode extends \Eloquent {}
}

