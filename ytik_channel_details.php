<?php
/**
 * Plugin Name: YouTube Channel Dashboard
 * Description: Fetches YouTube channel details using a shortcode.
 * Version: 1.0
 * Author: Imran Khan
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Main plugin class.
class YouTube_Channel_Dashboard {

    private $api_key;

    public function __construct() {
        // Initialize API Key.
        $this->api_key = 'AIzaSyDtiW-frbCBOQ5sE55KwPP8SuZzYzHaeaY45454'; // Replace with your API key.

        // Register shortcode.
        add_shortcode( 'youtube_dashboard', [ $this, 'render_dashboard' ] );
    }

    /**
     * Fetch channel data from YouTube API.
     *
     * @param string $channel_id YouTube channel ID.
     * @return array|false
     */
    private function fetch_channel_data( $channel_id ) {
        $url = sprintf(
            'https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet&id=%s&key=%s',
            $channel_id,
            $this->api_key
        );

        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $data['items'][0] ) ) {
            return $data['items'][0];
        }

        return false;
    }

    /**
     * Render shortcode output.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_dashboard( $atts ) {
        $atts = shortcode_atts( [
            'channel_id' => '',
        ], $atts, 'youtube_dashboard' );

        if ( empty( $atts['channel_id'] ) ) {
            return '<p>Please provide a valid channel ID.</p>';
        }

        $channel_data = $this->fetch_channel_data( $atts['channel_id'] );

        if ( ! $channel_data ) {
            return '<p>Unable to fetch channel data. Please try again later.</p>';
        }
var_dump($channel_data);
        // Extract required data.
        $title       = $channel_data['snippet']['title'];
        $description = $channel_data['snippet']['description'];
        $subscribers = $channel_data['statistics']['subscriberCount'];
        $views       = $channel_data['statistics']['viewCount'];
        $videos      = $channel_data['statistics']['videoCount'];
        $country      = $channel_data['snippet']['country'];

        // Render output.
        ob_start();
        ?>
        <div class="youtube-channel-dashboard">
            <h3><?php echo esc_html( $title ); ?></h3>
            <p><?php echo esc_html( $description ); ?></p>
            <ul>
                <li><strong>Subscribers:</strong> <?php echo number_format( $subscribers ); ?></li>
                <li><strong>Total Views:</strong> <?php echo number_format( $views ); ?></li>
                <li><strong>Videos:</strong> <?php echo number_format( $videos ); ?></li>
                <li><strong>Country:</strong> <?php echo $country; ?></li>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize plugin.
new YouTube_Channel_Dashboard();
