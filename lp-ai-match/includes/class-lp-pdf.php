<?php
/**
 * PDF generation from HTML reports.
 *
 * Uses DomPDF for HTML-to-PDF conversion.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LP_PDF {

	/**
	 * Generate PDF from a report.
	 *
	 * @param int $report_id Report post ID.
	 * @return string|WP_Error PDF file path or error.
	 */
	public static function generate( $report_id ) {
		$report = get_post( $report_id );
		if ( ! $report || 'lp_report' !== $report->post_type ) {
			return new WP_Error( 'invalid_report', __( 'レポートが見つかりません。', 'lp-ai-match' ) );
		}

		$html = self::wrap_html( $report->post_title, $report->post_content );

		// Try DomPDF if available.
		$pdf_path = self::generate_with_dompdf( $html, $report_id );
		if ( $pdf_path && ! is_wp_error( $pdf_path ) ) {
			update_post_meta( $report_id, '_lp_pdf_path', $pdf_path );
			return $pdf_path;
		}

		// Fallback: save as HTML file for download.
		$upload_dir = wp_upload_dir();
		$pdf_dir    = $upload_dir['basedir'] . '/lp-reports/';
		if ( ! file_exists( $pdf_dir ) ) {
			wp_mkdir_p( $pdf_dir );
			// Protect directory.
			file_put_contents( $pdf_dir . '.htaccess', 'deny from all' );
			file_put_contents( $pdf_dir . 'index.php', '<?php // Silence is golden.' );
		}

		$filename = 'report-' . $report_id . '-' . wp_generate_password( 8, false ) . '.html';
		$filepath = $pdf_dir . $filename;

		file_put_contents( $filepath, $html );
		update_post_meta( $report_id, '_lp_pdf_path', $filepath );

		return $filepath;
	}

	/**
	 * Generate PDF using DomPDF.
	 *
	 * @param string $html      HTML content.
	 * @param int    $report_id Report ID for filename.
	 * @return string|WP_Error File path or error.
	 */
	private static function generate_with_dompdf( $html, $report_id ) {
		// Check if DomPDF is available (via Composer autoload).
		$autoload = LP_AI_MATCH_PLUGIN_DIR . 'vendor/autoload.php';
		if ( ! file_exists( $autoload ) ) {
			return new WP_Error( 'no_dompdf', __( 'DomPDFがインストールされていません。', 'lp-ai-match' ) );
		}

		require_once $autoload;

		if ( ! class_exists( 'Dompdf\Dompdf' ) ) {
			return new WP_Error( 'no_dompdf', __( 'DomPDFクラスが見つかりません。', 'lp-ai-match' ) );
		}

		$upload_dir = wp_upload_dir();
		$pdf_dir    = $upload_dir['basedir'] . '/lp-reports/';
		if ( ! file_exists( $pdf_dir ) ) {
			wp_mkdir_p( $pdf_dir );
			file_put_contents( $pdf_dir . '.htaccess', 'deny from all' );
			file_put_contents( $pdf_dir . 'index.php', '<?php // Silence is golden.' );
		}

		$dompdf = new \Dompdf\Dompdf();
		$dompdf->loadHtml( $html );
		$dompdf->setPaper( 'A4', 'portrait' );
		$dompdf->render();

		$filename = 'report-' . $report_id . '-' . wp_generate_password( 8, false ) . '.pdf';
		$filepath = $pdf_dir . $filename;

		file_put_contents( $filepath, $dompdf->output() );

		return $filepath;
	}

	/**
	 * Wrap report content in full HTML document for PDF.
	 *
	 * @param string $title   Report title.
	 * @param string $content Report HTML content.
	 * @return string Full HTML document.
	 */
	private static function wrap_html( $title, $content ) {
		return '<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>' . esc_html( $title ) . '</title>
<style>
@page { margin: 20mm; }
body {
	font-family: "Hiragino Kaku Gothic Pro", "Yu Gothic", "Meiryo", sans-serif;
	font-size: 14px;
	line-height: 1.8;
	color: #333;
}
h2 { color: #e91e63; font-size: 22px; border-bottom: 2px solid #f8bbd0; padding-bottom: 8px; }
h3 { color: #9c27b0; font-size: 18px; margin-top: 24px; }
.lp-report__total-score {
	text-align: center;
	margin: 20px 0;
}
.lp-report__score-number {
	font-size: 72px;
	font-weight: bold;
	color: #e91e63;
}
.lp-report__score-unit {
	font-size: 24px;
	color: #666;
}
.lp-report__score-label {
	text-align: center;
	font-size: 16px;
	color: #9c27b0;
	margin-bottom: 30px;
}
.lp-report__score-bar {
	display: flex;
	align-items: center;
	margin: 10px 0;
}
.lp-report__bar-label {
	width: 100px;
	font-size: 13px;
}
.lp-report__bar {
	height: 20px;
	background: linear-gradient(to right, #f8bbd0, #e91e63);
	border-radius: 10px;
	flex: 1;
}
.lp-report__bar-value {
	width: 60px;
	text-align: right;
	font-weight: bold;
}
.lp-report__marriage-graph {
	display: flex;
	justify-content: space-around;
	align-items: flex-end;
	height: 200px;
	margin: 20px 0;
	padding: 10px;
	border-bottom: 1px solid #ddd;
}
.lp-report__graph-bar {
	display: flex;
	flex-direction: column;
	align-items: center;
	width: 50px;
}
.lp-report__graph-fill {
	width: 30px;
	background: linear-gradient(to top, #f8bbd0, #e91e63);
	border-radius: 5px 5px 0 0;
}
.lp-report__graph-year { font-size: 12px; margin-top: 4px; }
.lp-report__graph-value { font-size: 11px; font-weight: bold; }
</style>
</head>
<body>
<h1>' . esc_html( $title ) . '</h1>
' . $content . '
<footer style="margin-top:40px;text-align:center;color:#999;font-size:11px;">
韓国式AI運命マッチング | LP AI Match
</footer>
</body>
</html>';
	}

	/**
	 * Serve PDF for download.
	 *
	 * @param int $report_id Report post ID.
	 */
	public static function serve_download( $report_id ) {
		$filepath = get_post_meta( $report_id, '_lp_pdf_path', true );

		if ( ! $filepath || ! file_exists( $filepath ) ) {
			// Regenerate.
			$filepath = self::generate( $report_id );
			if ( is_wp_error( $filepath ) ) {
				wp_die( esc_html( $filepath->get_error_message() ) );
			}
		}

		$ext = pathinfo( $filepath, PATHINFO_EXTENSION );
		$mime = ( 'pdf' === $ext ) ? 'application/pdf' : 'text/html';

		header( 'Content-Type: ' . $mime );
		header( 'Content-Disposition: attachment; filename="compatibility-report.' . $ext . '"' );
		header( 'Content-Length: ' . filesize( $filepath ) );
		readfile( $filepath );
		exit;
	}
}
