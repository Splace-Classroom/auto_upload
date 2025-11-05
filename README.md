# Auto Upload Block for Moodle

A Moodle block plugin that automatically uploads files to an external API endpoint when files are uploaded to courses.

## Features

- **Admin Only Access**: Block is only visible and accessible to site administrators
- **On/Off Toggle**: Easy enable/disable functionality for auto upload
- **API Testing**: Built-in API connection testing
- **Automatic Upload**: Monitors file uploads in courses and automatically sends them to external API
- **Course & Module Context**: Includes course_id and module_id in API requests

## Installation

1. Copy the `auto_upload` folder to `/blocks/auto_upload/` in your Moodle installation
2. Visit Site Administration → Notifications to complete the installation
3. Go to Site Administration → Plugins → Blocks → Auto Upload to configure settings

## Configuration

### Global Settings

- **Enable auto upload**: Turn the auto upload functionality on/off
- **API Endpoint URL**: Configure the external API endpoint (default: http://165.22.62.163:5000/uploads)

### Block Settings

- **Block title**: Customize the block title
- Add the block to any page where administrators need quick access to controls

## API Integration

The plugin sends files to the configured endpoint using HTTP POST with form-data containing:

- `course_id`: The Moodle course ID where the file was uploaded
- `module_id`: The Moodle module ID (if applicable, 0 otherwise)
- `file`: The actual file content

### API Endpoint Requirements

- Accept HTTP POST requests
- Support multipart/form-data for file uploads
- Expected fields: course_id, module_id, file

## Usage

1. **Add the Block**: Add the Auto Upload block to a page (only admins can see it)
2. **Configure Settings**: Set up the API endpoint in plugin settings
3. **Test Connection**: Use the "Test API Connection" button to verify connectivity
4. **Enable Auto Upload**: Toggle the auto upload feature on
5. **Monitor**: Files uploaded to courses will automatically be sent to your API

## Block Interface

The block displays:

- Current status (Enabled/Disabled)
- API endpoint URL
- Enable/Disable toggle button
- Test API connection button

## Events Monitored

The plugin monitors these Moodle events:

- File creation events
- Assignment submission events
- Resource creation/update events

## File Context Detection

The plugin automatically detects:

- **Course ID**: From the context where the file was uploaded
- **Module ID**: When files are uploaded within course modules
- Handles various context levels (course, module, etc.)

## Security

- Only site administrators can access the block
- Uses Moodle's built-in security tokens (sesskey)
- Follows Moodle coding standards and security practices

## Troubleshooting

### Block Not Visible

- Ensure you are logged in as a site administrator
- Check that the block is properly installed via Site Administration → Notifications

### API Test Fails

- Verify the API endpoint URL is correct
- Check network connectivity from your Moodle server
- Ensure the external API is running and accessible

### Files Not Uploading

- Verify auto upload is enabled in settings
- Check Moodle error logs for detailed error messages
- Test API connection first

## Development

### File Structure

```
blocks/auto_upload/
├── block_auto_upload.php      # Main block class
├── version.php                # Plugin version and metadata
├── settings.php               # Global configuration settings
├── edit_form.php             # Block instance configuration
├── toggle.php                # Enable/disable functionality
├── test_api.php              # API connection testing
├── db/
│   ├── access.php            # Capabilities definition
│   └── events.php            # Event observers
├── classes/
│   ├── observer.php          # Event handling and API upload
│   └── privacy/
│       └── provider.php      # GDPR compliance
└── lang/en/
    └── block_auto_upload.php # Language strings
```

## License

This plugin is licensed under the GNU GPL v3 or later.

## Support

For issues and feature requests, please check the Moodle logs and ensure your API endpoint is properly configured and accessible.
